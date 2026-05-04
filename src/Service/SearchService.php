<?php

namespace App\Service;

use App\Repository\RecipeRepository;
use App\Repository\SearchRepository;
use App\Traits\RecipeTrait;
use App\Utils\Utils;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Serializer\SerializerInterface;

class SearchService
{
    use RecipeTrait;

    private $recipeRepository;
    private $doctrine;

    public function __construct(ManagerRegistry $doctrine, RecipeRepository $recipeRepository)
    {
        $this->recipeRepository = $recipeRepository;
        $this->doctrine = $doctrine;
    }

    public function searchRecipes(Request $request, SerializerInterface $serializer): Response
    {
        $name = $request->query->get('name', '');
        $categories = $request->query->all('categories');

        $recipes = $this->recipeRepository->searchRecipes($name, $categories);

        return new Response(
            Utils::serializeData($recipes, $this->getRecipeGroups(), $serializer),
            Response::HTTP_OK,
            ['Content-Type' => 'application/json']
        );
    }
}