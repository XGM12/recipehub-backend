<?php

declare(strict_types=1);

namespace App\Controller;

use App\Repository\RecipeRepository;
use App\Repository\UserRepository;
use App\Service\RecipeService;
use App\Service\UserService;
use App\Utils\Utils;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Contracts\Cache\CacheInterface;

class RecipesController extends AbstractController
{
    private $recipeRepository;
    private $userRepository;
    private $recipeService;

    public function __construct(RecipeRepository $recipeRepository, UserRepository $userRepository, RecipeService $recipeService) {
        $this->recipeRepository = $recipeRepository;
        $this->userRepository = $userRepository;
        $this->recipeService = $recipeService;
    }

    public function getSystemRecipes(Request $request, SerializerInterface $serializer, CacheInterface $cache): Response
    {
        Utils::checkRequestMethod($request, "GET");
        return $this->recipeService->getSystemRecipes($serializer, $cache);
    }

    public function getSystemRecipe(Request $request, SerializerInterface $serializer): Response
    {
        Utils::checkRequestMethod($request, "GET");
        return $this->recipeService->getSystemRecipe($request->get('id'), $serializer);
    }

    public function userRecipes(Request $request, SerializerInterface $serializer, CacheInterface $cache): Response
    {
        Utils::checkRequestMethod($request, "GET", "POST");

        $user = $this->userRepository->findByIdOrNull($request->get('id'));

        if (!$user)
            return new Response("User not found", Response::HTTP_NOT_FOUND);

        if ($request->getMethod() == "POST")
            return $this->recipeService->createRecipe($request, $serializer, $cache, $user);

        return $this->recipeService->getUserRecipes($user, $serializer);
    }

    public function deleteUserRecipe(Request $request, SerializerInterface $serializer, CacheInterface $cache): Response
    {
        Utils::checkRequestMethod($request, "DELETE", "PUT");

        $recipe = $this->recipeRepository->findByIdOrNull($request->get("recipeId"));

        if (!$recipe)
            return new Response("Recipe not found", Response::HTTP_NOT_FOUND);

        if ($request->getMethod() == "DELETE")
            return $this->recipeService->deleteRecipe($recipe, $cache);

        return $this->recipeService->updateRecipe($request, $recipe, $serializer, $cache);
    }
}