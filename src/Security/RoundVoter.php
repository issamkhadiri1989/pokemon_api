<?php

declare(strict_types=1);

namespace App\Security;

use App\Entity\Round;
use App\Entity\Trainer;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;

class RoundVoter extends Voter
{
    protected function supports(string $attribute, mixed $subject): bool
    {
        return $subject instanceof Round && 'CAN_FIGHT' === $attribute;
    }

    /**
     * @param string $attribute
     * @param Round $subject
     * @param TokenInterface $token
     *
     * @return bool
     */
    protected function voteOnAttribute(string $attribute, mixed $subject, TokenInterface $token): bool
    {
        /** @var Trainer $user */
        $user = $token->getUser();

        return true;
    }
}