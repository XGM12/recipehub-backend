<?php

namespace App\Traits;

use App\Entity\Recipes;
use App\Entity\RecipeSteps;
use Doctrine\ORM\EntityManagerInterface;

trait RecipeTrait
{
    public function getRecipeGroups(): array
    {
        return ['groups' => [
            'user_recipe:read',
            'recipe:read',
            'ingredients:read',
            'recipe_steps:read'
        ]];
    }

    private function persistSteps(array $steps, Recipes $recipe, EntityManagerInterface $em): void
    {
        foreach ($steps as $stepData) {
            $step = new RecipeSteps();
            $step->setStepOrder($stepData['step_order']);
            $step->setDescription($stepData['description']);
            $step->setRecipe($recipe);
            $recipe->getSteps()->add($step);
            $em->persist($step);
        }
    }

    private function clearSteps(Recipes $recipe, EntityManagerInterface $em): void
    {
        foreach ($recipe->getSteps() as $step) {
            $em->remove($step);
        }
        $recipe->getSteps()->clear();
    }

    private function clearIngredients(Recipes $recipe, EntityManagerInterface $em): void
    {
        foreach ($recipe->getRecipeIngredients() as $recipeIngredient) {
            $em->remove($recipeIngredient);
        }
        $recipe->getRecipeIngredients()->clear();
    }
}