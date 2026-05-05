<?php

namespace App\Service;

use App\Entity\Ingredients;
use App\Entity\RecipeIngredients;
use App\Entity\Recipes;
use App\Entity\Users;
use App\Repository\RecipeRepository;
use App\Repository\UserRepository;
use App\Traits\RecipeTrait;
use App\Utils\Utils;
use Doctrine\Persistence\ManagerRegistry;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Serializer\Normalizer\AbstractNormalizer;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Contracts\Cache\CacheInterface;
use Symfony\Contracts\Cache\ItemInterface;

class RecipeService
{
    use RecipeTrait;

    private $userRepository;
    private $recipeRepository;
    private $doctrine;
    private const CACHE_KEY = 'system_recipes';

    public function __construct(ManagerRegistry $doctrine, RecipeRepository $recipeRepository, UserRepository $userRepository)
    {
        $this->doctrine = $doctrine;
        $this->recipeRepository = $recipeRepository;
        $this->userRepository = $userRepository;
    }

    public function getSystemRecipes(SerializerInterface $serializer, CacheInterface $cache): Response
    {
        // [SOSTENIBILIDAD] Caché Redis de 5 minutos para evitar queries repetidas a MySQL.
        // Reduce el procesamiento del servidor y el consumo energético asociado.
        $recipes = $cache->get(self::CACHE_KEY, function (ItemInterface $item) {
            $item->expiresAfter(300);
            return $this->recipeRepository->findSystemRecipes();
        });

        Utils::checkNotNull($recipes);

        return new Response(
            Utils::serializeData($recipes, $this->getRecipeGroups(), $serializer),
            Response::HTTP_OK,
            ['Content-Type' => 'application/json']
        );
    }

    public function getSystemRecipe(string $id, SerializerInterface $serializer, CacheInterface $cache): Response
    {
        // [SOSTENIBILIDAD] Caché Redis por receta individual para evitar queries repetidas.
        // Reduce el procesamiento del servidor y el consumo energético asociado.
        $recipe = $cache->get('system_recipe_' . $id, function (ItemInterface $item) use ($id) {
            $item->expiresAfter(300);
            return $this->recipeRepository->findSystemRecipeById($id);
        });

        if (!$recipe)
            return new Response("Recipe not found", Response::HTTP_NOT_FOUND);

        return new Response(
            Utils::serializeData($recipe, $this->getRecipeGroups(), $serializer),
            Response::HTTP_OK,
            ['Content-Type' => 'application/json']
        );
    }

    public function getUserRecipes(Users $user, SerializerInterface $serializer): Response
    {
        $recipes = $this->recipeRepository->findByUser($user);

        return new Response(
            Utils::serializeData($recipes, $this->getRecipeGroups(), $serializer),
            Response::HTTP_OK,
            ['Content-Type' => 'application/json']
        );
    }

    public function createRecipe(Request $request, SerializerInterface $serializer, CacheInterface $cache, Users $user): Response
    {
        $body = $request->getContent();
        $bodyDecoded = json_decode($body, true);
        $recipe = new Recipes();

        $serializer->deserialize($body, Recipes::class, 'json', [
            AbstractNormalizer::OBJECT_TO_POPULATE => $recipe,
            AbstractNormalizer::ALLOW_EXTRA_ATTRIBUTES => ['id'],
            'groups' => 'recipe:write'
        ]);

        $recipe->setCreatedBy($user);
        $user->getRecipe()->add($recipe);

        $em = $this->doctrine->getManager();
        $em->persist($recipe);

        if (isset($bodyDecoded['steps']))
            $this->persistSteps($bodyDecoded['steps'], $recipe, $em);

        if (isset($bodyDecoded['ingredients']))
            $this->persistIngredients($bodyDecoded['ingredients'], $recipe, $em);

        $em->flush();

        // [SOSTENIBILIDAD] Se invalida la caché al crear una receta nueva
        // para garantizar consistencia de datos sin consultas innecesarias.
        $cache->delete(self::CACHE_KEY);

        return new Response(
            Utils::serializeData($recipe, $this->getRecipeGroups(), $serializer),
            Response::HTTP_CREATED,
            ['Content-Type' => 'application/json']
        );
    }

    public function deleteRecipe(Recipes $recipe, CacheInterface $cache): Response
    {
        $em = $this->doctrine->getManager();
        $em->remove($recipe);
        $em->flush();

        // [SOSTENIBILIDAD] Invalidamos la caché al eliminar una receta.
        $cache->delete(self::CACHE_KEY);
        $cache->delete('system_recipe_' . $recipe->getId());

        return new Response("Recipe was deleted", Response::HTTP_NO_CONTENT);
    }

    public function updateRecipe(Request $request, Recipes $recipe, SerializerInterface $serializer, CacheInterface $cache): Response
    {
        $body = $request->getContent();
        $bodyDecoded = json_decode($body, true);
        $em = $this->doctrine->getManager();

        $serializer->deserialize($body, Recipes::class, 'json', [
            AbstractNormalizer::OBJECT_TO_POPULATE => $recipe,
            AbstractNormalizer::ALLOW_EXTRA_ATTRIBUTES => ['id'],
            'groups' => 'recipe:write'
        ]);

        $this->clearSteps($recipe, $em);
        if (isset($bodyDecoded['steps']))
            $this->persistSteps($bodyDecoded['steps'], $recipe, $em);

        $this->clearIngredients($recipe, $em);
        if (isset($bodyDecoded['ingredients']))
            $this->persistIngredients($bodyDecoded['ingredients'], $recipe, $em);

        $em->flush();

        // [SOSTENIBILIDAD] Invalidamos la caché al editar una receta
        // para que los datos cacheados no queden obsoletos.
        $cache->delete(self::CACHE_KEY);
        $cache->delete('system_recipe_' . $recipe->getId());

        return new Response(
            Utils::serializeData($recipe, $this->getRecipeGroups(), $serializer),
            Response::HTTP_OK,
            ['Content-Type' => 'application/json']
        );
    }

    private function persistIngredients(array $ingredients, Recipes $recipe, ObjectManager $em): void
    {
        foreach ($ingredients as $ingredientData) {
            $ingredient = $this->doctrine
                ->getRepository(Ingredients::class)
                ->findOneBy(['name' => $ingredientData['name']]);

            if (!$ingredient) {
                $ingredient = new Ingredients();
                $ingredient->setName($ingredientData['name']);
                $em->persist($ingredient);
            }

            $recipeIngredient = new RecipeIngredients();
            $recipeIngredient->setRecipe($recipe);
            $recipeIngredient->setIngredient($ingredient);
            $recipeIngredient->setQuantity($ingredientData['quantity'] ?? '');
            $recipe->getRecipeIngredients()->add($recipeIngredient);
            $em->persist($recipeIngredient);
        }
    }

    public function getUserFavourites(Users $user, SerializerInterface $serializer): Response
    {
        return new Response(
            Utils::serializeData($user->getRecipe(), $this->getRecipeGroups(), $serializer),
            Response::HTTP_OK,
            ['Content-Type' => 'application/json']
        );
    }

    public function addFavourite(Users $user, Recipes $recipe, SerializerInterface $serializer): Response
    {
        if ($this->recipeRepository->isFavourite($user, $recipe))
            return new Response("Recipe already in favourites", Response::HTTP_CONFLICT);

        $user->getRecipe()->add($recipe);

        $em = $this->doctrine->getManager();
        $em->persist($user);
        $em->flush();

        return new Response(
            Utils::serializeData(
                $recipe,
                $this->getRecipeGroups(),
                $serializer
            ),
            Response::HTTP_CREATED,
            ['Content-Type' => 'application/json']
        );
    }

    public function removeFavourite(Users $user, Recipes $recipe): Response
    {
        if (!$this->recipeRepository->isFavourite($user, $recipe))
            return new Response("Recipe not in favourites", Response::HTTP_NOT_FOUND);

        $user->getRecipe()->removeElement($recipe);

        $em = $this->doctrine->getManager();
        $em->persist($user);
        $em->flush();

        return new Response("Recipe removed from favourites", Response::HTTP_NO_CONTENT);
    }
}