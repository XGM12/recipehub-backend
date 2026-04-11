<?php

declare(strict_types=1);

namespace App\Controller;

use App\Entity\Users;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Serializer\Normalizer\AbstractNormalizer;
use Symfony\Component\Serializer\SerializerInterface;

class UserController extends AbstractController
{
    public function login(Request $request, SerializerInterface $serializer): Response
    {
        if ($request->getMethod() != "POST")
            return new Response(
                "HTTP Method is not valid",
                Response::HTTP_BAD_REQUEST
            );

        $data = $request->getContent();

        $serializer->deserialize($data, Users::class, 'json', [
            AbstractNormalizer::OBJECT_TO_POPULATE => $data,
            AbstractNormalizer::ALLOW_EXTRA_ATTRIBUTES => ['id'],
            'groups' => 'login:write'
        ]);

        $data = json_decode($data, true);

        $user = $this->getDoctrine()
            ->getRepository(Users::class)
            ->findOneBy(['email' => $data['email'], 'password' => $data['password']]);

        if (!$user)
            return new Response(
                "User doesn't exists",
                Response::HTTP_NOT_FOUND
            );

        return new Response(
            $serializer->serialize(
                $user,
                'json',
                ['groups' => 'login:read']
            ),
            Response::HTTP_OK,
            ['Content-Type' => 'application/json']
        );
    }

    public function register(Request $request, SerializerInterface $serializer): Response
    {
        if ($request->getMethod() != 'POST')
            return new Response(
                "HTTP Method is not valid",
                Response::HTTP_BAD_REQUEST
            );

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

        $data = $serializer->serialize(
            $user,
            'json',
            ['groups' => 'login:read']
        );

        return new Response(
            $data,
            Response::HTTP_CREATED,
            ['Content-Type' => 'application/json']
        );
    }

    public function delete(Request $request): Response
    {
        if ($request->getMethod() != "DELETE")
            return new Response(
                "HTTP Method is not valid",
                Response::HTTP_BAD_REQUEST
            );

        $id = $request->get("id");

        $user = $this->getDoctrine()
            ->getRepository(Users::class)
            ->findOneBy(['id' => $id]);

        if (!$user)
            return new Response(
                "User not found",
                Response::HTTP_NOT_FOUND
            );

        $entityManager = $this->getDoctrine()
            ->getManager();

        $entityManager->remove($user);
        $entityManager->flush();

        return new Response("User deleted", Response::HTTP_NO_CONTENT);
    }
}
