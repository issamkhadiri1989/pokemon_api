<?php

declare(strict_types=1);

namespace App\Service\Duel;

use App\Entity\Duel;
use Symfony\Component\Workflow\Registry;

/**
 * This service is responsible to define and manage statues of the duel.
 */
class DuelStatusDefiner
{
    public function __construct(private readonly Registry $registry)
    {

    }

    /**
     * Starts new duel.
     *
     * @param Duel $duel
     *
     * @return void
     */
    public function startDuel(Duel $duel): void
    {
        $this->setDuelState($duel, 'start');
    }

    /**
     * Closes a non started duel. A non started duel means that the challenger has opened but the opponent
     * did not accept it yet.
     *
     * @param Duel $duel
     *
     * @return void
     */
    public function closeUnStartedDuel(Duel $duel): void
    {
        $this->setDuelState($duel, 'close');
    }

    /**
     * Ends the duel.
     *
     * @param Duel $duel
     *
     * @return void
     */
    public function endDuel(Duel $duel): void
    {
        $this->setDuelState($duel, 'end');
    }

    /**
     * Checks the ability of changing the duel's state by providing the transition name.
     *
     * @param Duel $duel
     * @param string $transition
     *
     * @return void
     */
    private function setDuelState(Duel $duel, string $transition): void
    {
        $stateMachine = $this->registry->get($duel, 'duel_process');
        $isAbleToStart = $stateMachine->can($duel, $transition);
        if (true === $isAbleToStart) {
            $stateMachine->apply($duel, $transition);
        }
    }
}
