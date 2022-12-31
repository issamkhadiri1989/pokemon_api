<?php

declare(strict_types=1);

namespace App\Event;

use App\Entity\Duel;
use App\Entity\Trainer;
use App\Enum\DuelStates;
use App\Service\Duel\DuelStatusDefiner;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpKernel\Event\ViewEvent;

class DuelListener
{
    private DuelStatusDefiner $definer;

    public function __construct(private readonly Security $security, DuelStatusDefiner $definer)
    {
        $this->definer = $definer;
    }

    /**
     * Initiate some properties of the duel like the date and the state of the match.
     *
     * @param ViewEvent $event
     *
     * @return void
     */
    public function completeDuelProperties(ViewEvent $event): void
    {
        $result = $event->getControllerResult();
        if ($result instanceof Duel) {
            $request = $event->getRequest();
            if ($request->isMethod('POST') === true) {
                $this->completeDuelData($result);
            }

            if ($request->isMethod('PATCH') === true) {
                $routeName = $request->attributes->get('_route');
                match ($routeName) {
                    '_api_/duel/{id}/accept_patch' => $this->definer->startDuel($result),
                    '_api_/duel/{id}/close_patch' => $this->definer->closeUnStartedDuel($result),
                };
            }
        }
    }

    /**
     * Completes the data of the given duel.
     *
     * @param Duel $duel
     *
     * @return void
     */
    private function completeDuelData(Duel $duel): void
    {
        /** @var Trainer $challenger */
        $challenger = $this->security->getUser();
        $state = DuelStates::OPENED;
        $duel->setChallenger($challenger)
            ->setDuelTime(new \DateTime())
            ->setState($state->value);
    }
}
