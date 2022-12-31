<?php

declare(strict_types=1);

namespace App\Validator\Constraints;

use Symfony\Component\Validator\Constraint;

#[\Attribute]
/**
 * @Annotation
 */
class PerformAttackConstraint extends Constraint
{
    public string $moveUnsupportedMessage = "The move you are trying to perform is not supported by the Pokemon";

    public string $trainerNotAllowedMessage = "The current trainer is not allowed to perform any attack in this round";

    public string $wrongPokemonMessage = "The selected Pokemon is not allowed to be used in this round.";

    public function getTargets(): string
    {
        return Constraint::CLASS_CONSTRAINT;
    }
}
