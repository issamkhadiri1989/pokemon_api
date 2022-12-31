<?php

declare(strict_types=1);

namespace App\Entity;

use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\Patch;
use ApiPlatform\Metadata\Post;
use App\Processor\DuelPokemonSelector;
use App\Repository\RoundRepository;
use  App\Validator\Constraints as Constraint;
use App\Validator\RoundNumberCheck;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Serializer\Annotation\SerializedName;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: RoundRepository::class)]
#[ApiResource(
    operations: [
        new Get(normalizationContext: ['groups' => ['round:read']]), // get infos about a given round
        new Post( // enables persisting a new round
            normalizationContext: ['groups' => ['round:read']],
            securityPostDenormalize: "is_granted('CAN_FIGHT', object)"
        ),
        new Patch(// used by the trainers to select a Pokemon during the match
            uriTemplate: "/rounds/{id}/select",
            normalizationContext: ['groups' => ['round:read']],
            denormalizationContext: ['groups' => ['round:patch']],
            processor: DuelPokemonSelector::class,
        ),
    ],
)]
//#[Assert\Callback([RoundNumberCheck::class, 'validate'])]
class Round
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(['round:read'])]
    private ?int $id = null;

    #[ORM\ManyToOne(inversedBy: 'rounds')]
    #[ORM\JoinColumn(nullable: false)]
    #[Assert\NotNull]
    #[Groups(['round:read'])]
    private ?Duel $duel = null;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: true)]
    #[Groups(['round:read'])]
    private ?BagItem $challengerPokemon = null;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: true)]
    #[Groups(['round:read'])]
    private ?BagItem $opponentPokemon = null;

    #[Assert\NotNull(groups: ['round:read']), Constraint\BagOwnerConstraint]
    #[SerializedName("select_pokemon")]
    #[Groups(['round:patch'])]
    private ?BagItem $selectedPokemon = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getDuel(): ?Duel
    {
        return $this->duel;
    }

    public function setDuel(?Duel $duel): self
    {
        $this->duel = $duel;

        return $this;
    }

    public function getChallengerPokemon(): ?BagItem
    {
        return $this->challengerPokemon;
    }

    public function setChallengerPokemon(?BagItem $challengerPokemon): self
    {
        $this->challengerPokemon = $challengerPokemon;

        return $this;
    }

    public function getOpponentPokemon(): ?BagItem
    {
        return $this->opponentPokemon;
    }

    public function setOpponentPokemon(?BagItem $opponentPokemon): self
    {
        $this->opponentPokemon = $opponentPokemon;

        return $this;
    }

    /**
     * @return ?BagItem
     */
    public function getSelectedPokemon(): ?BagItem
    {
        return $this->selectedPokemon;
    }

    /**
     * @param ?BagItem $selectedPokemon
     *
     * @return Round
     */
    public function setSelectedPokemon(?BagItem $selectedPokemon): self
    {
        $this->selectedPokemon = $selectedPokemon;

        return $this;
    }
}
