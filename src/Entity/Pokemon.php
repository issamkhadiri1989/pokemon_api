<?php

declare(strict_types=1);

namespace App\Entity;

use ApiPlatform\Doctrine\Orm\Filter\SearchFilter;
use ApiPlatform\Metadata\ApiFilter;
use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\Metadata\Link;
use ApiPlatform\Metadata\Put;
use App\Repository\PokemonRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;

/**
 * This class represents a Pokemon instance.
 */
#[ORM\Entity(repositoryClass: PokemonRepository::class)]
#[ApiResource(
    operations: [
        new Get(normalizationContext: ['groups' => ['profile', 'evolution']]),
        new GetCollection(normalizationContext: ['groups' => ['profile']]),
        new Put()
    ]
)]
#[ApiFilter(SearchFilter::class, properties: ['code' => 'exact'])]
#[ApiResource(
    uriTemplate: '/category/{categoryId}/pokemon',
    operations: [new GetCollection()],
    uriVariables: [
        'categoryId' => new Link(fromProperty: 'pokemon', fromClass: Category::class),
    ],
    normalizationContext: ['groups' => ['profile']],
)]
class Pokemon
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(['profile'])]
    private ?int $id = null;

    #[ORM\Column(length: 100)]
    #[Groups(['profile', 'evolution'])]
    private ?string $code = null;

    #[ORM\Column(length: 255)]
    #[Groups(['profile', 'evolution', 'round:read'])]
    private ?string $name = null;

    #[ORM\Column(length: 255)]
    private ?string $mainType = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $secondType = null;

    #[ORM\Column(length: 255, nullable: true)]
    #[Groups(['profile', 'round:read'])]
    private ?string $picture = null;

    #[ORM\ManyToOne(inversedBy: 'pokemon')]
    #[Groups(['profile', 'round:read'])]
    private ?Category $mainCategory = null;

    #[ORM\ManyToOne]
    #[Groups(['profile', 'round:read'])]
    private ?Category $secondaryCategory = null;

    #[ORM\ManyToOne(targetEntity: self::class)]
    #[Groups(['evolution'])]
    private ?self $nextEvolution = null;

    #[ORM\Column(nullable: true)]
    #[Groups(['profile'])]
    private ?int $evolutionLevel = null;

    #[ORM\OneToMany(mappedBy: 'pokemon', targetEntity: Ability::class, orphanRemoval: true)]
    #[Groups(['profile'])]
    private Collection $abilities;

    #[ORM\Column(nullable: true)]
    private ?int $health = null;

    #[ORM\Column(nullable: true)]
    private ?int $attack = null;

    #[ORM\Column(nullable: true)]
    private ?int $defense = null;

    #[ORM\Column(nullable: true)]
    private ?int $speed = null;

    public function __construct()
    {
        $this->abilities = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getCode(): ?string
    {
        return $this->code;
    }

    public function setCode(string $code): self
    {
        $this->code = $code;

        return $this;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    public function getMainType(): ?string
    {
        return $this->mainType;
    }

    public function setMainType(string $mainType): self
    {
        $this->mainType = $mainType;

        return $this;
    }

    public function getSecondType(): ?string
    {
        return $this->secondType;
    }

    public function setSecondType(string $secondType): self
    {
        $this->secondType = $secondType;

        return $this;
    }

    public function getPicture(): ?string
    {
        return $this->picture;
    }

    public function setPicture(?string $picture): self
    {
        $this->picture = $picture;

        return $this;
    }

    public function getMainCategory(): ?Category
    {
        return $this->mainCategory;
    }

    public function setMainCategory(?Category $mainCategory): self
    {
        $this->mainCategory = $mainCategory;

        return $this;
    }

    public function getSecondaryCategory(): ?Category
    {
        return $this->secondaryCategory;
    }

    public function setSecondaryCategory(?Category $secondaryCategory): self
    {
        $this->secondaryCategory = $secondaryCategory;

        return $this;
    }

    public function getNextEvolution(): ?self
    {
        return $this->nextEvolution;
    }

    public function setNextEvolution(?self $nextEvolution): self
    {
        $this->nextEvolution = $nextEvolution;

        return $this;
    }

    public function getEvolutionLevel(): ?int
    {
        return $this->evolutionLevel;
    }

    public function setEvolutionLevel(?int $evolutionLevel): self
    {
        $this->evolutionLevel = $evolutionLevel;

        return $this;
    }

    /**
     * @return Collection<int, Ability>
     */
    public function getAbilities(): Collection
    {
        return $this->abilities;
    }

    public function addAbility(Ability $ability): self
    {
        if (!$this->abilities->contains($ability)) {
            $this->abilities->add($ability);
            $ability->setPokemon($this);
        }

        return $this;
    }

    public function removeAbility(Ability $ability): self
    {
        if ($this->abilities->removeElement($ability)) {
            // set the owning side to null (unless already changed)
            if ($ability->getPokemon() === $this) {
                $ability->setPokemon(null);
            }
        }

        return $this;
    }

    public function getHealth(): ?int
    {
        return $this->health;
    }

    public function setHealth(?int $health): self
    {
        $this->health = $health;

        return $this;
    }

    public function getAttack(): ?int
    {
        return $this->attack;
    }

    public function setAttack(?int $attack): self
    {
        $this->attack = $attack;

        return $this;
    }

    public function getDefense(): ?int
    {
        return $this->defense;
    }

    public function setDefense(?int $defense): self
    {
        $this->defense = $defense;

        return $this;
    }

    public function getSpeed(): ?int
    {
        return $this->speed;
    }

    public function setSpeed(?int $speed): self
    {
        $this->speed = $speed;

        return $this;
    }
}
