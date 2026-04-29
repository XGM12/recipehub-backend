<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;

/**
 * RecipeSteps
 *
 * @ORM\Table(name="recipe_steps", indexes={@ORM\Index(name="recipe_id", columns={"recipe_id"})})
 * @ORM\Entity
 */
class RecipeSteps
{
    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     * @Groups({"recipe_steps:read"})
     */
    private $id;

    /**
     * @var int
     *
     * @ORM\Column(name="step_order", type="integer", nullable=false)
     * @Groups({"recipe_steps:read"})
     */
    private $stepOrder;

    /**
     * @var string
     *
     * @ORM\Column(name="description", type="text", length=65535, nullable=false)
     * @Groups({"recipe_steps:read"})
     */
    private $description;

    /**
     * @var Recipes
     *
     * @ORM\ManyToOne(targetEntity="Recipes")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="recipe_id", referencedColumnName="id")
     * })
     */
    private $recipe;

    public function getId(): int
    {
        return $this->id;
    }

    public function getStepOrder(): int
    {
        return $this->stepOrder;
    }

    public function setStepOrder(int $stepOrder): void
    {
        $this->stepOrder = $stepOrder;
    }

    public function getDescription(): string
    {
        return $this->description;
    }

    public function setDescription(string $description): void
    {
        $this->description = $description;
    }

    public function getRecipe(): Recipes
    {
        return $this->recipe;
    }

    public function setRecipe(Recipes $recipe): void
    {
        $this->recipe = $recipe;
    }


}
