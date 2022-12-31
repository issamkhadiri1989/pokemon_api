<?php

declare(strict_types=1);

namespace App\Event;

use ApiPlatform\Symfony\EventListener\EventPriorities;
use App\Entity\Bag;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Component\HttpKernel\Event\ViewEvent;
use Symfony\Bundle\SecurityBundle\Security;

class BagSubscriber implements EventSubscriberInterface
{
    public function __construct(private readonly Security $security)
    {
    }

    public static function getSubscribedEvents(): array
    {
        return [
            KernelEvents::VIEW => ['assignUser', EventPriorities::PRE_WRITE],
        ];
    }

    /**
     * Assigns the current user, if logged in, to the current bag.
     *
     * @param ViewEvent $event
     *
     * @return void
     */
    public function assignUser(ViewEvent $event): void
    {
        $data = $event->getControllerResult();
        if($data instanceof Bag && ($user = $this->security->getUser()) !== null) {
            $data->setTrainer($user);
        }
    }
}