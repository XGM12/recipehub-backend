<?php

declare(strict_types=1);

namespace App\Controller;

use App\Entity\Users;
use App\Utils\Utils;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Serializer\Normalizer\AbstractNormalizer;
use Symfony\Component\Serializer\SerializerInterface;

class UserController extends AbstractController
{
    public function login(Request $request, SerializerInterface $serializer): Response
    {
        Utils::checkRequestMethod($request, "POST");

        $data = json_decode($request->getContent(), true);

        $user = $this->getDoctrine()
            ->getRepository(Users::class)
            ->findOneBy(['email' => $data['email'], 'password' => $data['password']]);

        try {
            Utils::checkNotNull($user);
        } catch (NotFoundHttpException $_) {
            return new Response(
                "User not found",
                Response::HTTP_NOT_FOUND
            );
        }

        $data = Utils::serializeData($user, ['groups' => 'login:read'], $serializer);

        return new Response(
            $data,
            Response::HTTP_OK,
            ['Content-Type' => 'application/json']
        );
    }

    public function register(Request $request, SerializerInterface $serializer): Response
    {
        Utils::checkRequestMethod($request, "POST");

        $data = $request->getContent();

        $user = new Users();

        $serializer->deserialize($data, Users::class, 'json', [
            AbstractNormalizer::OBJECT_TO_POPULATE => $user,
            AbstractNormalizer::ALLOW_EXTRA_ATTRIBUTES => ['id'],
            'groups' => 'register:write'
        ]);

        $entityManager = $this->getDoctrine()
            ->getManager();

        $entityManager->persist($user);
        $entityManager->flush();

        $data = Utils::serializeData($user, ['groups' => 'login:read'], $serializer);

        return new Response(
            $data,
            Response::HTTP_CREATED,
            ['Content-Type' => 'application/json']
        );
    }

    public function getUserById(Request $request, SerializerInterface $serializer): Response
    {
        Utils::checkRequestMethod($request, "GET", "DELETE");

        $entityManager = $this->getDoctrine()
            ->getManager();

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

        if ($request->getMethod() == "DELETE") {
            $entityManager->remove($user);
            $entityManager->flush();

            return new Response("User deleted", Response::HTTP_NO_CONTENT);
        }

        $data = Utils::serializeData($user, ['groups' => 'login:read'], $serializer);

        return new Response(
            $data,
            Response::HTTP_OK,
            ['Content-Type' => 'application/json']
        );
    }
}
