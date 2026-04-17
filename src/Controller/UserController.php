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

        $this->checkUser($user);

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
        Utils::checkRequestMethod($request, "DELETE");

        $id = $request->get("id");

        $user = $this->getDoctrine()
            ->getRepository(Users::class)
            ->findOneBy(['id' => $id]);

        $this->checkUser($user);

        $entityManager = $this->getDoctrine()
            ->getManager();

        $entityManager->remove($user);
        $entityManager->flush();

        return new Response("User deleted", Response::HTTP_NO_CONTENT);
    }

    public function getUserById(Request $request, SerializerInterface $serializer): Response
    {
        Utils::checkRequestMethod($request, "GET");

        $id = $request->get("id");

        $user = $this->getDoctrine()
            ->getRepository(Users::class)
            ->findOneBy(['id' => $id]);

        $this->checkUser($user);

        $data = $serializer->serialize(
            $user,
            'json',
            ['group' => 'login:read']
        );

        return new Response(
            $data,
            Response::HTTP_OK,
            ['Content-Type' => 'application/json']
        );
    }



    private function checkUser(Users $user)
    {
        if (!$user)
            throw new NotFoundHttpException("User not found");
    }
}
