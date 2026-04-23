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
use Symfony\Component\Serializer\Normalizer\AbstractNormalizer;
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

    public function userRecipes(Request $request, SerializerInterface $serializer): Response
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
            $entityManager->flush();

            $data = Utils::serializeData($recipe, ['groups' => ['user_recipe:read', 'recipe:read']], $serializer);

            return new Response(
                $data,
                Response::HTTP_CREATED,
                ['Content-Type' => 'application/json']
            );
        }

        $data = Utils::serializeData($user->getRecipe(), ['groups' => 'recipe:read'], $serializer);

        return new Response(
            $data,
            Response::HTTP_OK,
            ['Content-Type' => 'application/json']
        );
    }

    public function deleteUserRecipe(Request $request, SerializerInterface $serializer): Response
    {
        Utils::checkRequestMethod($request, "DELETE", "PUT");

        $userId = $request->get("userId");
        $recipeId = $request->get("recipeId");

        $user = $this->getDoctrine()
            ->getRepository(Users::class)
            ->findOneBy(['id' => $userId]);

        $recipe = $this->getDoctrine()
            ->getRepository(Recipes::class)
            ->findOneBy(['id' => $recipeId]);

        $entityManager = $this->getDoctrine()->getManager();

        if ($request->getMethod() == "DELETE") {
            $user->getRecipe()->removeElement($recipe);
            $user->setRecipe($user->getRecipe());

            $entityManager->persist($user);
            $entityManager->flush();

            return new Response("Recipe was deleted", Response::HTTP_NO_CONTENT);
        }

        $body = $request->getContent();

        $serializer->deserialize($body, Recipes::class, 'json', [
            AbstractNormalizer::OBJECT_TO_POPULATE => $recipe,
            AbstractNormalizer::ALLOW_EXTRA_ATTRIBUTES => ['id'],
            'groups' => 'recipe:write'
        ]);

        $entityManager->persist($recipe);
        $entityManager->flush();

        $data = Utils::serializeData($recipe, ['groups' => 'recipe:read'], $serializer);

        return new Response(
            $data,
            Response::HTTP_OK,
            ['Content-Type' => 'application/json']
        );
    }
}
