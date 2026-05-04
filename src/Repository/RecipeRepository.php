<?php

namespace App\Repository;

use App\Entity\Recipes;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

class RecipeRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Recipes::class);
    }

    public function findByIdOrNull(string $id): ?Recipes
    {
        return $this->findOneBy(['id' => $id]);
    }

    public function findSystemRecipes(): array
    {
        return $this->findBy(['createdBy' => null]);
    }

    public function findSystemRecipeById(string $id): ?Recipes
    {
        return $this->findOneBy(['createdBy' => null, 'id' => $id]);
    }
}