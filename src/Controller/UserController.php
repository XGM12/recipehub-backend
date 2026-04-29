<?php

declare(strict_types=1);

namespace App\Controller;

use App\Service\UserService;
use App\Utils\Utils;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Serializer\SerializerInterface;

class UserController extends AbstractController
{
    private $userService;

    public function __construct(UserService $userService) {
        $this->userService = $userService;
    }

    public function login(Request $request, SerializerInterface $serializer): Response
    {
        Utils::checkRequestMethod($request, "POST");
        return $this->userService->login($request, $serializer);
    }

    public function register(Request $request, SerializerInterface $serializer): Response
    {
        Utils::checkRequestMethod($request, "POST");
        return $this->userService->register($request, $serializer);
    }

    public function getUserById(Request $request, SerializerInterface $serializer): Response
    {
        Utils::checkRequestMethod($request, "GET", "DELETE");

        if ($request->getMethod() == "DELETE")
            return $this->userService->deleteUser($request->get("id"));

        return $this->userService->getUserById($request->get("id"), $serializer);
    }
}