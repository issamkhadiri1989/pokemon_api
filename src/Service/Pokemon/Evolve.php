<?php

declare(strict_types=1);

namespace App\Service\Pokemon;

use App\Entity\Ability;
use App\Entity\BagItem;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\SecurityBundle\Security;

class Evolve
{
    public function __construct(private readonly Security $security, private readonly EntityManagerInterface $manager)
    {
    }

    /**
     * Performing the evolving with ability levels.
     *
     * @param BagItem $pokeball
     *
     * @return void
     */
    public function performEvolving(BagItem $pokeball): void
    {
        $connectedTrainer = $this->security->getUser();
        if ($pokeball->getBag()->getTrainer() === $connectedTrainer && $this->isAbleToEvolve($pokeball) === true) {
            // Evolve the current Pokemon to the next stage
            $evolving = $pokeball->getPokemon()->getNextEvolution();
            $pokeball->setPokemon($evolving);
        }
    }

    /**
     * Makes the pokemon held in the pokeball in argument learn new moves.
     *
     * @param BagItem $pokeball
     *
     * @return void
     */
    public function learnNewAbility(BagItem $pokeball): void
    {
        /** @var Ability[] $abilities */
        $abilities = $this->filterNewAbility($pokeball);
        if (empty($abilities) === false) {
            foreach ($abilities as $ability) {
                $pokeball->addAbility($ability);
                $this->manager->persist($ability);
            }
            $this->manager->flush();
        }
    }

    /**
     * Checks if the pokemon in the given pokeball is able to evolvle.
     *
     * @param BagItem $pokeball
     *
     * @return bool
     */
    private function isAbleToEvolve(BagItem $pokeball): bool
    {
        $currentLevel = $pokeball->getLevel();
        $evolutionLevel = $pokeball->getPokemon()->getEvolutionLevel();

        return $currentLevel === $evolutionLevel;
    }

    /**
     * Gets the next new abilities to be learnt by the Pokemon held in the given Pokeball.
     *
     * @param BagItem $pokeball
     *
     * @return iterable
     */
    private function filterNewAbility(BagItem $pokeball): iterable
    {
        return $pokeball->getPokemon()
            ->getAbilities()
            ->filter(function (Ability $ability) use ($pokeball) {
                return $pokeball->getLevel() === $ability->getLevel()
                    && $ability->getPokemon() === $pokeball->getPokemon();
            });
    }
}
