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

    public function searchRecipes(string $name, array $categories = []): array
    {
        $qb = $this->createQueryBuilder('r')
            ->where('r.name LIKE :name')
            ->setParameter('name', '%' . $name . '%')
            ->orderBy('r.name', 'ASC');

        if (!empty($categories))
            $qb->andWhere('r.category IN (:categories)')
                ->setParameter('categories', $categories);

        return $qb->getQuery()->getResult();
    }
}