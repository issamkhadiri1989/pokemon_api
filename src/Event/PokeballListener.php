<?php

declare(strict_types=1);

namespace App\Event;

use ApiPlatform\Symfony\EventListener\EventPriorities;
use App\Entity\BagItem;
use App\Service\Pokemon\Evolve;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Event\ViewEvent;
use Symfony\Component\HttpKernel\KernelEvents;

class PokeballListener implements EventSubscriberInterface
{
    public function __construct(private readonly Evolve $evolve)
    {
    }

    public static function getSubscribedEvents(): array
    {
        return [
            KernelEvents::VIEW => ['evolve', EventPriorities::PRE_WRITE],
        ];
    }

    /**
     * This event will check first whether the Pokemon is able to evolve first.
     *
     * @param ViewEvent $event
     *
     * @return void
     */
    public function evolve(ViewEvent $event): void
    {
        $result = $event->getControllerResult();
        $request = $event->getRequest();
        if ($result instanceof BagItem && $event->getRequest()->isMethod('PATCH') === true) {
            $routeName = $request->attributes->get('_route');
            if (\in_array($routeName, ['_api_/evolve/{id}_patch', '_api_/levelup/{id}_patch']) === true) {
                if ('_api_/levelup/{id}_patch' === $routeName) {
                    $result->setLevel($result->getLevel() + 1);
                }
                $this->evolve->learnNewAbility($result);
                $this->evolve->performEvolving($result);
            }
        }
    }
}
