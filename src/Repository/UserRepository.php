<?php

namespace App\Repository;

use App\Entity\Users;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

class UserRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Users::class);
    }

    public function findByIdOrNull(string $id): ?Users
    {
        return $this->findOneBy(['id' => $id]);
    }

    public function findByCredentials(string $email, string $password): ?Users
    {
        return $this->findOneBy(['email' => $email, 'password' => $password]);
    }
}