<?php

declare(strict_types=1);

namespace App\Controller;

use App\Entity\Recipes;
use App\Entity\Users;
use App\Utils\Utils;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Serializer\SerializerInterface;

class RecipesController extends AbstractController
{
    public function getSystemRecipes(Request $request, SerializerInterface $serializer): Response
    {
        Utils::checkRequestMethod($request, "GET");

        $recipes = $this->getDoctrine()
            ->getRepository(Recipes::class)
            ->findAll();

        Utils::checkNotNull($recipes);

        $data = Utils::serializeData($recipes, ['groups' => ['recipe:read', 'user_recipe:read']], $serializer);

        return new Response(
            $data,
            Response::HTTP_OK,
            ['Content-Type' => 'application/json']
        );
    }

    public function getAllUserLikedRecipes(Request $request, SerializerInterface $serializer): Response
    {
        Utils::checkRequestMethod($request, "GET");

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

        $data = $serializer->serialize(
            $user->getRecipe(),
            'json',
            ['groups' => 'recipe:read']
        );

        return new Response(
            $data,
            Response::HTTP_OK,
            ['Content-Type' => 'application/json']
        );
    }
}
