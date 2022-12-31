<?php

declare(strict_types=1);

namespace App\ApiResource\Model;

use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Post;
use App\Entity\Ability;
use App\Entity\BagItem;
use App\Entity\Round;
use App\Validator\Constraints\PerformAttackConstraint;
use Symfony\Component\Validator\Constraints as Assert;

#[ApiResource(
    operations: [new Post()],
    security: "is_granted('ROLE_USER')"
)]
#[PerformAttackConstraint]
class Attack
{
    #[Assert\NotNull]
    private ?Round $round = null;

    #[Assert\NotNull]
    private ?Ability $move = null;

    #[Assert\NotNull]
    private ?BagItem $pokemon = null;

    /**
     * @return ?Round
     */
    public function getRound(): ?Round
    {
        return $this->round;
    }

    /**
     * @param ?Round $round
     *
     * @return Attack
     */
    public function setRound(?Round $round): self
    {
        $this->round = $round;

        return $this;
    }

    /**
     * @return ?Ability
     */
    public function getMove(): ?Ability
    {
        return $this->move;
    }

    /**
     * @param ?Ability $move
     *
     * @return Attack
     */
    public function setMove(?Ability $move): self
    {
        $this->move = $move;

        return $this;
    }

    // validate that the given ability belongs to the Pokemon

    /**
     * @return ?BagItem
     */
    public function getPokemon(): ?BagItem
    {
        return $this->pokemon;
    }

    /**
     * @param ?BagItem $pokemon
     *
     * @return Attack
     */
    public function setPokemon(?BagItem $pokemon): self
    {
        $this->pokemon = $pokemon;

        return $this;
    }
}
