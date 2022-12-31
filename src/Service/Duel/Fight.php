<?php

declare(strict_types=1);

namespace App\Service\Duel;

use App\Entity\BagItem;
use App\Entity\Round;
use App\Entity\Trainer;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\Security\Core\User\UserInterface;

class Fight
{
    public function __construct(private readonly Security $security, private readonly EntityManagerInterface $manager, private readonly DuelStatusDefiner $statusDefiner)
    {
    }

    /**
     * Selects the right Pokémon depending on whether the user connected is the challenger or the opponent.
     *
     * @param Round $round
     *
     * @return ?BagItem
     */
    public function selectPokemon(Round $round): ?BagItem
    {
        $duel = $round->getDuel();

        return match (true) {
            $this->security->getUser() === $duel->getChallenger() => $round->getChallengerPokemon(),
            $this->security->getUser() === $duel->getOpponent() => $round->getOpponentPokemon(),
            default => null
        };
    }

    /**
     * Verifies that the right Pokémon  has been chosen by the right trainer.
     *
     * @param UserInterface $trainer
     * @param BagItem $pokemon
     * @param Round $round
     *
     * @return bool
     */
    public function trainerHasChosenPokemon(UserInterface $trainer, BagItem $pokemon, Round $round): bool
    {
        $duel = $round->getDuel();

        if ($trainer === $duel->getChallenger() && $pokemon === $round->getChallengerPokemon()) {
            return true;
        }

        if ($trainer === $duel->getOpponent() && $pokemon === $round->getOpponentPokemon()) {
            return true;
        }

        return false;
    }

    /**
     * Inflicts the damage to the Pokémon.
     *
     * @param Round $round
     * @param BagItem $pokemon
     * @param int $damage
     *
     * @return void
     */
    public function inflictDamage(Round $round, BagItem $pokemon, int $damage): void
    {
        $pokemon->inflictDamage($damage);
        if ($pokemon->getHealthPoint() === 0) { // means that the Pokémon is KO
            /** @var Trainer $trainer */
            $trainer = $this->security->getUser();
            $duel = $round->getDuel();
            $duel->setWinner($trainer);
            $this->statusDefiner->endDuel($duel);
        }
        $this->manager->flush();
    }
}