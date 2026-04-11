<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Recipes
 *
 * @ORM\Table(name="recipes", indexes={@ORM\Index(name="created_by", columns={"created_by"})})
 * @ORM\Entity
 */
class Recipes
{
    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $id;

    /**
     * @var string
     *
     * @ORM\Column(name="name", type="string", length=255, nullable=false)
     */
    private $name;

    /**
     * @var string|null
     *
     * @ORM\Column(name="image_url", type="string", length=500, nullable=true)
     */
    private $imageUrl;

    /**
     * @var int
     *
     * @ORM\Column(name="prep_time_minutes", type="integer", nullable=false)
     */
    private $prepTimeMinutes;

    /**
     * @var string
     *
     * @ORM\Column(name="category", type="string", length=0, nullable=false)
     */
    private $category;

    /**
     * @var DateTime|null
     *
     * @ORM\Column(name="created_at", type="datetime", nullable=true, options={"default"="CURRENT_TIMESTAMP"})
     */
    private $createdAt = 'CURRENT_TIMESTAMP';

    /**
     * @var Users
     *
     * @ORM\ManyToOne(targetEntity="Users")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="created_by", referencedColumnName="id")
     * })
     */
    private $createdBy;

    /**
     * @var \Doctrine\Common\Collections\Collection
     *
     * @ORM\ManyToMany(targetEntity="Ingredients", inversedBy="recipe")
     * @ORM\JoinTable(name="recipe_ingredients",
     *   joinColumns={
     *     @ORM\JoinColumn(name="recipe_id", referencedColumnName="id")
     *   },
     *   inverseJoinColumns={
     *     @ORM\JoinColumn(name="ingredient_id", referencedColumnName="id")
     *   }
     * )
     */
    private $ingredient = array();

    /**
     * @var \Doctrine\Common\Collections\Collection
     *
     * @ORM\ManyToMany(targetEntity="Users", mappedBy="recipe")
     */
    private $user = array();

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->ingredient = new \Doctrine\Common\Collections\ArrayCollection();
        $this->user = new \Doctrine\Common\Collections\ArrayCollection();
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function setName(string $name): void
    {
        $this->name = $name;
    }

    public function getImageUrl(): ?string
    {
        return $this->imageUrl;
    }

    public function setImageUrl(?string $imageUrl): void
    {
        $this->imageUrl = $imageUrl;
    }

    public function getPrepTimeMinutes(): int
    {
        return $this->prepTimeMinutes;
    }

    public function setPrepTimeMinutes(int $prepTimeMinutes): void
    {
        $this->prepTimeMinutes = $prepTimeMinutes;
    }

    public function getCategory(): string
    {
        return $this->category;
    }

    public function setCategory(string $category): void
    {
        $this->category = $category;
    }

    /**
     * @return DateTime|string|null
     */
    public function getCreatedAt()
    {
        return $this->createdAt;
    }

    /**
     * @param DateTime|string|null $createdAt
     */
    public function setCreatedAt($createdAt): void
    {
        $this->createdAt = $createdAt;
    }

    public function getCreatedBy(): ?Users
    {
        return $this->createdBy;
    }

    public function setCreatedBy(Users $createdBy): void
    {
        $this->createdBy = $createdBy;
    }

    /**
     * @return \Doctrine\Common\Collections\ArrayCollection|\Doctrine\Common\Collections\Collection
     */
    public function getIngredient()
    {
        return $this->ingredient;
    }

    /**
     * @param \Doctrine\Common\Collections\ArrayCollection|\Doctrine\Common\Collections\Collection $ingredient
     */
    public function setIngredient($ingredient): void
    {
        $this->ingredient = $ingredient;
    }

    /**
     * @return \Doctrine\Common\Collections\ArrayCollection|\Doctrine\Common\Collections\Collection
     */
    public function getUser()
    {
        return $this->user;
    }

    /**
     * @param \Doctrine\Common\Collections\ArrayCollection|\Doctrine\Common\Collections\Collection $user
     */
    public function setUser($user): void
    {
        $this->user = $user;
    }

}
