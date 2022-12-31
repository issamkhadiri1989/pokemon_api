<?php

declare(strict_types=1);

namespace App\Validator\Constraints;

use Symfony\Component\Validator\Constraint;

/**
 * @Annotation
 */
#[\Attribute]
class NotCurrentUserConstraint extends Constraint
{
    public string $message = 'This user should not be the same as the connected user';
}