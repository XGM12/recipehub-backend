<?php

namespace App\Service;

use App\Entity\Users;
use App\Repository\UserRepository;
use App\Traits\UserTrait;
use App\Utils\Utils;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Serializer\Normalizer\AbstractNormalizer;
use Symfony\Component\Serializer\SerializerInterface;

class UserService
{
    use UserTrait;

    private $doctrine;
    private $userRepository;

    public function __construct(
        ManagerRegistry $doctrine,
        UserRepository $userRepository
    ) {
        $this->userRepository = $userRepository;
        $this->doctrine = $doctrine;
    }

    public function login(Request $request, SerializerInterface $serializer): Response
    {
        $data = json_decode($request->getContent(), true);

        $user = $this->userRepository->findByCredentials($data['email'], $data['password']);

        if (!$user)
            return new Response("User not found", Response::HTTP_NOT_FOUND);

        return new Response(
            Utils::serializeData($user, $this->getUserGroups(), $serializer),
            Response::HTTP_OK,
            ['Content-Type' => 'application/json']
        );
    }

    public function register(Request $request, SerializerInterface $serializer): Response
    {
        $user = new Users();

        $serializer->deserialize($request->getContent(), Users::class, 'json', [
            AbstractNormalizer::OBJECT_TO_POPULATE => $user,
            AbstractNormalizer::ALLOW_EXTRA_ATTRIBUTES => ['id'],
            'groups' => 'register:write'
        ]);

        $em = $this->doctrine->getManager();
        $em->persist($user);
        $em->flush();

        return new Response(
            Utils::serializeData($user, $this->getUserGroups(), $serializer),
            Response::HTTP_CREATED,
            ['Content-Type' => 'application/json']
        );
    }

    public function getUserById(string $id, SerializerInterface $serializer): Response
    {
        $user = $this->userRepository->findByIdOrNull($id);

        if (!$user)
            return new Response("User not found", Response::HTTP_NOT_FOUND);

        return new Response(
            Utils::serializeData($user, $this->getUserGroups(), $serializer),
            Response::HTTP_OK,
            ['Content-Type' => 'application/json']
        );
    }

    public function deleteUser(string $id): Response
    {
        $user = $this->userRepository->findByIdOrNull($id);

        if (!$user)
            return new Response("User not found", Response::HTTP_NOT_FOUND);

        $em = $this->doctrine->getManager();
        $em->remove($user);
        $em->flush();

        return new Response("User deleted", Response::HTTP_NO_CONTENT);
    }
}