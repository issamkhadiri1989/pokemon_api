<?php

declare(strict_types=1);

namespace App\Security;

use App\Entity\BagItem;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;

class PokeballVoter extends Voter
{
    private const CAN_PUT_IN_BAG = 'CAN_PUT_IN_BAG';

    public function __construct(private readonly Security $security)
    {
    }

    protected function supports(string $attribute, mixed $subject): bool
    {
        return $subject instanceof BagItem && self::CAN_PUT_IN_BAG === $attribute;
    }

    protected function voteOnAttribute(string $attribute, mixed $subject, TokenInterface $token): bool
    {
        $isOwner = $this->isTrainerOwner($subject);

        if (self::CAN_PUT_IN_BAG === $attribute) {
            return true === $isOwner;
        }

        return false;
    }

    private function isTrainerOwner(BagItem $subject): bool
    {
        $owner = $subject->getBag()->getTrainer();
        $connectedUser = $this->security->getUser();

        return $owner === $connectedUser;
    }
}
