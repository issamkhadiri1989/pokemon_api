<?php

declare(strict_types=1);

namespace App\Entity;

use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\Metadata\Link;
use ApiPlatform\Metadata\Patch;
use ApiPlatform\Metadata\Post;
use App\Repository\DuelRepository;
use App\Validator\Constraints as CustomAssert;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * This class represents the duel.
 */
#[ORM\Entity(repositoryClass: DuelRepository::class)]
#[ApiResource(
    operations: [
        new Get(security: "is_granted('ROLE_USER')"), // displays full details of a given match
        new Post(), // challenge a trainer by creating a new duel
        // Displays the list of matches
        new GetCollection(
            uriTemplate: "/trainers/{id}/matches",
            uriVariables: ['id' => new Link(fromProperty: 'duels', fromClass: Trainer::class)]
        ),
        new Patch(
            uriTemplate: "/duel/{id}/accept", // this endpoint will accept the challenge
            normalizationContext: ['groups' => ['duel:read']],
            denormalizationContext: ['groups' => ['duel:edit']],
            security: "object.getOpponent() == user",
//            output: false,
        ),
        new Patch( // the challenger can also cancel a match that he has opened before.
            uriTemplate: "/duel/{id}/close",
            denormalizationContext: ['groups' => ['duel:change:status']],
            securityPostDenormalize: "object.getChallenger() == user",
        ),
        new GetCollection(filters: [])
    ],
)]
class Duel
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(['duel:read'])]
    private ?int $id = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    #[Groups(['duel:read'])]
    private ?\DateTimeInterface $duelTime = null;

    #[ORM\ManyToOne(inversedBy: 'duels')]
    #[ORM\JoinColumn(nullable: false)]
    #[CustomAssert\NotCurrentUserConstraint(groups: ['duel:challenge'])]
    private ?Trainer $challenger = null;

    #[ORM\ManyToOne]
    #[Assert\NotNull]
    private ?Trainer $opponent = null;

    #[ORM\Column(length: 255)]
    #[Groups(['duel:edit', 'duel:read'])]
    private mixed $state = null;

    #[ORM\ManyToOne]
    private ?Trainer $winner = null;

    #[ORM\Column(nullable: true)]
    #[Assert\Range(min: 1, max: 6), Assert\NotNull]
    private ?int $nbPokemonAllowed = null;

    #[ORM\OneToMany(mappedBy: 'duel', targetEntity: Round::class)]
    private Collection $rounds;

    public function __construct()
    {
        $this->rounds = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getDuelTime(): ?\DateTimeInterface
    {
        return $this->duelTime;
    }

    public function setDuelTime(\DateTimeInterface $duelTime): self
    {
        $this->duelTime = $duelTime;

        return $this;
    }

    public function getChallenger(): ?Trainer
    {
        return $this->challenger;
    }

    public function setChallenger(?Trainer $challenger): self
    {
        $this->challenger = $challenger;

        return $this;
    }

    public function getOpponent(): ?Trainer
    {
        return $this->opponent;
    }

    public function setOpponent(?Trainer $opponent): self
    {
        $this->opponent = $opponent;

        return $this;
    }

    public function getState(): mixed
    {
        return $this->state;
    }

    public function setState(mixed $state, $context = []): self
    {
        $this->state = $state;

        return $this;
    }

    public function getWinner(): ?Trainer
    {
        return $this->winner;
    }

    public function setWinner(?Trainer $winner): self
    {
        $this->winner = $winner;

        return $this;
    }

    public function getNbPokemonAllowed(): ?int
    {
        return $this->nbPokemonAllowed;
    }

    public function setNbPokemonAllowed(?int $nbPokemonAllowed): self
    {
        $this->nbPokemonAllowed = $nbPokemonAllowed;

        return $this;
    }

    /**
     * @return Collection<int, Round>
     */
    public function getRounds(): Collection
    {
        return $this->rounds;
    }

    public function addRound(Round $round): self
    {
        if (!$this->rounds->contains($round)) {
            $this->rounds->add($round);
            $round->setDuel($this);
        }

        return $this;
    }

    public function removeRound(Round $round): self
    {
        if ($this->rounds->removeElement($round)) {
            // set the owning side to null (unless already changed)
            if ($round->getDuel() === $this) {
                $round->setDuel(null);
            }
        }

        return $this;
    }
}
