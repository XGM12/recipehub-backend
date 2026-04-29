<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;

/**
 * RecipeIngredients
 *
 * @ORM\Table(name="recipe_ingredients")
 * @ORM\Entity
 */
class RecipeIngredients
{
    /**
     * @var Recipes
     *
     * @ORM\Id
     * @ORM\ManyToOne(targetEntity="Recipes", inversedBy="recipeIngredients")
     * @ORM\JoinColumn(name="recipe_id", referencedColumnName="id")
     */
    private $recipe;

    /**
     * @var Ingredients
     *
     * @ORM\Id
     * @ORM\ManyToOne(targetEntity="Ingredients")
     * @ORM\JoinColumn(name="ingredient_id", referencedColumnName="id")
     * @Groups({"ingredients:read"})
     */
    private $ingredient;

    /**
     * @var string
     *
     * @ORM\Column(name="quantity", type="string", length=50, nullable=false)
     * @Groups({"ingredients:read"})
     */
    private $quantity;

    public function getRecipe(): Recipes
    {
        return $this->recipe;
    }

    public function setRecipe(Recipes $recipe): void
    {
        $this->recipe = $recipe;
    }

    public function getIngredient(): Ingredients
    {
        return $this->ingredient;
    }

    public function setIngredient(Ingredients $ingredient): void
    {
        $this->ingredient = $ingredient;
    }

    public function getQuantity(): string
    {
        return $this->quantity;
    }

    public function setQuantity(string $quantity): void
    {
        $this->quantity = $quantity;
    }
}