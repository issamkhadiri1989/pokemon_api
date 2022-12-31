<?php

declare(strict_types=1);

namespace App\Validator;

use App\Entity\Round;
use Symfony\Component\Validator\Context\ExecutionContextInterface;

/**
 * This class validates the maximum rounds of a given match.
 */
class RoundNumberCheck
{
    public static function validate($object, ExecutionContextInterface $context, ?array $payload): void
    {
        if (!$object instanceof Round) {
            return;
        }

        // get the instance of Duel
        $duel = $object->getDuel();

        $actualNumberOfRounds = $duel->getRounds()->count();
        // this checks simply that the number of rounds has exceeded the number of Pokémon already set in the duel
        // however, the real check is to take into consideration how many Pokémon are KO and OK.
        if ($duel->getNbPokemonAllowed() < ($actualNumberOfRounds + 1)) {
            $context->buildViolation('The maximum of rounds {{ max }} has been reached.')
                ->setParameter('{{ max }}', (string) $actualNumberOfRounds)
                ->atPath('duel')
                ->addViolation();
        }
    }
}
