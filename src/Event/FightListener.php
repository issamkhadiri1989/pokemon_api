<?php

declare(strict_types=1);

namespace App\Event;

use ApiPlatform\Symfony\EventListener\EventPriorities;
use App\ApiResource\Model\Attack;
use App\Entity\BagItem;
use App\Entity\Round;
use App\Entity\Trainer;
use App\Exception\NonSelectedOpponentPokemonException;
use App\Service\Duel\Fight;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Event\ViewEvent;
use Symfony\Component\HttpKernel\KernelEvents;

class FightListener implements EventSubscriberInterface
{
    public function __construct(private readonly Security $security, private readonly Fight $fightManager)
    {
    }

    public static function getSubscribedEvents(): array
    {
        return [
            KernelEvents::VIEW => ['performAttack', EventPriorities::PRE_WRITE],
        ];
    }

    /**
     * Perform attack.
     *
     * @param ViewEvent $event
     *
     * @return void
     */
    public function performAttack(ViewEvent $event): void
    {
        $result = $event->getControllerResult();
        if ($result instanceof Attack) {
            // do some logic about the attack
            $round = $result->getRound(); // the round
            $pokemon = $result->getPokemon(); // the selected Pokémon
            $opponentPokemon = $this->getOpponentPokemon($round);
            if (null !== $opponentPokemon) {
                $this->fightManager->inflictDamage($round, $opponentPokemon, \rand(0, $pokemon->getHealthPoint()));
            } else {
                throw new NonSelectedOpponentPokemonException();
            }
        }
    }

    /**
     * Gets the opposing trainer's Pokémon depending on the given Round.
     *
     * @param Round $round
     *
     * @return ?BagItem
     */
    private function getOpponentPokemon(Round $round): ?BagItem
    {
        $duel = $round->getDuel();
        /** @var Trainer $trainer */
        $trainer = $this->security->getUser();

        return match (true) {
            $trainer === $duel->getChallenger() => $round->getOpponentPokemon(),
            $trainer === $duel->getOpponent()  => $round->getChallengerPokemon(),
            default => throw new \UnhandledMatchError(),
        };
    }
}
