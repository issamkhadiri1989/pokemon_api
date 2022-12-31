<?php

declare(strict_types=1);

namespace App\Entity;

use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\Patch;
use ApiPlatform\Metadata\Post;
use App\Repository\BagItemRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;

/**
 * This class represents Poke-ball which holds a Pokémon instance with a specific level.
 */
#[ORM\Entity(repositoryClass: BagItemRepository::class)]
#[ApiResource(
    operations: [
        new Patch(
            uriTemplate: "/evolve/{id}",
            normalizationContext: ['groups' => ['summary']],
            securityPostDenormalize: "is_granted('CAN_PUT_IN_BAG', object)"
        ),
        new Patch(
            uriTemplate: "/levelup/{id}",
            defaults: ['_api_operation_name' => 'pokemon_levelup'],
            normalizationContext: ['groups' => ['summary']],
            securityPostDenormalize: "is_granted('CAN_PUT_IN_BAG', object)"
        ),
        new Post(
            uriTemplate: "/catch",
            securityPostDenormalize: "is_granted('CAN_PUT_IN_BAG', object)"
        ),
        new Get(
            uriTemplate: "/pokeball/{id}",
            normalizationContext: ['groups' => ['round:read', 'round:patch']],
            securityPostDenormalize: "object.getBag().getTrainer() == user",
        ),
    ],
    security: "is_granted('ROLE_USER')"
)]
class BagItem
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(['summary', 'admin'])]
    private ?int $id = null;

    #[ORM\ManyToOne]
    #[Groups(['admin', 'summary', 'round:read'])]
    private ?Pokemon $pokemon = null;

    #[ORM\Column]
    #[Groups(['admin', 'summary', 'round:read'])]
    private ?int $level = null;

    #[ORM\ManyToOne(inversedBy: 'bagItems')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Bag $bag = null;

    #[ORM\ManyToMany(targetEntity: Ability::class)]
    #[Groups(['admin', 'round:read'])]
    private Collection $abilities;

    #[ORM\Column(nullable: true)]
    private ?int $healthPoint = null;

    public function __construct()
    {
        $this->abilities = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getPokemon(): ?Pokemon
    {
        return $this->pokemon;
    }

    public function setPokemon(?Pokemon $pokemon): self
    {
        $this->pokemon = $pokemon;

        return $this;
    }

    public function getLevel(): ?int
    {
        return $this->level;
    }

    public function setLevel(int $level): self
    {
        $this->level = $level;

        return $this;
    }

    public function getBag(): ?Bag
    {
        return $this->bag;
    }

    public function setBag(?Bag $bag): self
    {
        $this->bag = $bag;

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
        }

        return $this;
    }

    public function removeAbility(Ability $ability): self
    {
        $this->abilities->removeElement($ability);

        return $this;
    }

    public function getHealthPoint(): ?int
    {
        return $this->healthPoint;
    }

    public function setHealthPoint(int $healthPoint): self
    {
        $this->healthPoint = $healthPoint;

        return $this;
    }

    public function inflictDamage(int $damage): void
    {
        $currentHealth = $this->getHealthPoint();
        $currentHealth -= $damage;
        if ($currentHealth < 0) {
            $currentHealth = 0;
        }
        $this->setHealthPoint($currentHealth);
    }
}
