<?php

declare(strict_types=1);

namespace App\Controller;

use App\Entity\Ingredients;
use App\Entity\RecipeIngredients;
use App\Entity\Recipes;
use App\Entity\RecipeSteps;
use App\Entity\Users;
use App\Utils\Utils;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Serializer\Normalizer\AbstractNormalizer;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Contracts\Cache\CacheInterface;
use Symfony\Contracts\Cache\ItemInterface;

class RecipesController extends AbstractController
{
    public function getSystemRecipes(Request $request, SerializerInterface $serializer, CacheInterface $cache): Response
    {
        Utils::checkRequestMethod($request, "GET");

        // [SOSTENIBILIDAD] Caché Redis de 5 minutos para evitar queries repetidas a MySQL.
        // Reduce el procesamiento del servidor y el consumo energético asociado.
        $recipes = $cache->get("system_recipes", function (ItemInterface $item) {
            $item->expiresAfter(300);
            return $this->getDoctrine()
                ->getRepository(Recipes::class)
                ->findBy(['createdBy' => null]);
        });

        Utils::checkNotNull($recipes);

        $data = Utils::serializeData(
            $recipes,
            ['groups' => [
                'user_recipe:read',
                'recipe:read',
                'ingredients:read',
                'recipe_steps:read'
            ]],
            $serializer
        );

        return new Response(
            $data,
            Response::HTTP_OK,
            ['Content-Type' => 'application/json']
        );
    }

    public function getSystemRecipe(Request $request, SerializerInterface $serializer): Response
    {
        Utils::checkRequestMethod($request, "GET");

        $id = $request->get('id');

        $recipe = $this->getDoctrine()
            ->getRepository(Recipes::class)
            ->findBy(['createdBy' => null, 'id' => $id]);

        try {
            Utils::checkNotNull($recipe);
        } catch (NotFoundHttpException $_) {
            return new Response(
                "Recipe not found",
                Response::HTTP_NOT_FOUND
            );
        }

        $data = Utils::serializeData(
            $recipe,
            ['groups' => [
                'user_recipe:read',
                'recipe:read',
                'ingredients:read',
                'recipe_steps:read'
            ]],
            $serializer
        );

        return new Response(
            $data,
            Response::HTTP_OK,
            ['Content-Type' => 'application/json']
        );
    }

    public function userRecipes(Request $request, SerializerInterface $serializer, CacheInterface $cache): Response
    {
        Utils::checkRequestMethod($request, "GET", "POST");

        $id = $request->get("id");

        $user = $this->getDoctrine()
            ->getRepository(Users::class)
            ->findOneBy(['id' => $id]);

        try {
            Utils::checkNotNull($user);
        } catch (NotFoundHttpException $_) {
            return new Response(
                "User not found",
                Response::HTTP_NOT_FOUND
            );
        }

        if ($request->getMethod() == "POST") {
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

            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($recipe);

            // Añadir pasos a la receta
            if (isset($bodyDecoded['steps']) && is_array($bodyDecoded['steps'])) {
                foreach ($bodyDecoded['steps'] as $stepData) {
                    $step = new RecipeSteps();
                    $step->setStepOrder($stepData['step_order']);
                    $step->setDescription($stepData['description']);
                    $step->setRecipe($recipe);
                    $recipe->getSteps()->add($step);
                    $entityManager->persist($step);
                }
            }

            // Ingredientes
            if (isset($bodyDecoded['ingredients']) && is_array($bodyDecoded['ingredients'])) {
                foreach ($bodyDecoded['ingredients'] as $ingredientData) {
                    $ingredient = $this->getDoctrine()
                        ->getRepository(Ingredients::class)
                        ->findOneBy(['name' => $ingredientData['name']]);

                    if (!$ingredient) {
                        $ingredient = new Ingredients();
                        $ingredient->setName($ingredientData['name']);
                        $entityManager->persist($ingredient);
                    }

                    $recipeIngredient = new RecipeIngredients();
                    $recipeIngredient->setRecipe($recipe);
                    $recipeIngredient->setIngredient($ingredient);
                    $recipeIngredient->setQuantity($ingredientData['quantity'] ?? '');
                    $recipe->getRecipeIngredients()->add($recipeIngredient);
                    $entityManager->persist($recipeIngredient);
                }
            }

            $entityManager->flush();

            // [SOSTENIBILIDAD] Se invalida la caché al crear una receta nueva
            // para garantizar consistencia de datos sin consultas innecesarias.
            $cache->delete("system_recipes");

            $data = Utils::serializeData(
                $recipe,
                ['groups' => [
                    'user_recipe:read',
                    'recipe:read',
                    'ingredients:read',
                    'recipe_steps:read'
                ]],
                $serializer
            );

            return new Response(
                $data,
                Response::HTTP_CREATED,
                ['Content-Type' => 'application/json']
            );
        }

        $data = Utils::serializeData(
            $user->getRecipe(),
            ['groups' => [
                'user_recipe:read',
                'recipe:read',
                'ingredients:read',
                'recipe_steps:read'
            ]],
            $serializer
        );

        return new Response(
            $data,
            Response::HTTP_OK,
            ['Content-Type' => 'application/json']
        );
    }

    public function deleteUserRecipe(Request $request, SerializerInterface $serializer, CacheInterface $cache): Response
    {
        Utils::checkRequestMethod($request, "DELETE", "PUT");

        $userId = $request->get("userId");
        $recipeId = $request->get("recipeId");

        $recipe = $this->getDoctrine()
            ->getRepository(Recipes::class)
            ->findOneBy(['id' => $recipeId]);

        $entityManager = $this->getDoctrine()->getManager();

        if ($request->getMethod() == "DELETE") {
            $entityManager->remove($recipe);
            $entityManager->flush();

            // [SOSTENIBILIDAD] Invalidamos la caché al eliminar una receta.
            $cache->delete('system_recipes');

            return new Response("Recipe was deleted", Response::HTTP_NO_CONTENT);
        }

        $body = $request->getContent();
        $bodyDecoded = json_decode($body, true);

        // Actualiza los campos básicos de la receta
        $serializer->deserialize($body, Recipes::class, 'json', [
            AbstractNormalizer::OBJECT_TO_POPULATE => $recipe,
            AbstractNormalizer::ALLOW_EXTRA_ATTRIBUTES => ['id'],
            'groups' => 'recipe:write'
        ]);

        // Borra los pasos actuales y los recrea
        foreach ($recipe->getSteps() as $step) {
            $entityManager->remove($step);
        }
        $recipe->getSteps()->clear();

        if (isset($bodyDecoded['steps']) && is_array($bodyDecoded['steps'])) {
            foreach ($bodyDecoded['steps'] as $stepData) {
                $step = new RecipeSteps();
                $step->setStepOrder($stepData['step_order']);
                $step->setDescription($stepData['description']);
                $step->setRecipe($recipe);
                $recipe->getSteps()->add($step);
                $entityManager->persist($step);
            }
        }

        // Borra los ingredientes actuales y los recrea
        foreach ($recipe->getRecipeIngredients() as $recipeIngredient) {
            $entityManager->remove($recipeIngredient);
        }
        $recipe->getRecipeIngredients()->clear();

        if (isset($bodyDecoded['ingredients']) && is_array($bodyDecoded['ingredients'])) {
            foreach ($bodyDecoded['ingredients'] as $ingredientData) {
                $ingredient = $this->getDoctrine()
                    ->getRepository(Ingredients::class)
                    ->findOneBy(['name' => $ingredientData['name']]);

                if (!$ingredient) {
                    $ingredient = new Ingredients();
                    $ingredient->setName($ingredientData['name']);
                    $entityManager->persist($ingredient);
                }

                $recipeIngredient = new RecipeIngredients();
                $recipeIngredient->setRecipe($recipe);
                $recipeIngredient->setIngredient($ingredient);
                $recipeIngredient->setQuantity($ingredientData['quantity'] ?? '');
                $recipe->getRecipeIngredients()->add($recipeIngredient);
                $entityManager->persist($recipeIngredient);
            }
        }

        $entityManager->flush();

        // [SOSTENIBILIDAD] Invalidamos la caché al editar una receta
        // para que los datos cacheados no queden obsoletos.
        $cache->delete('system_recipes');

        $data = Utils::serializeData(
            $recipe,
            ['groups' => [
                'recipe:read',
                'ingredients:read',
                'recipe_steps:read'
            ]],
            $serializer
        );

        return new Response(
            $data,
            Response::HTTP_OK,
            ['Content-Type' => 'application/json']
        );
    }
}
