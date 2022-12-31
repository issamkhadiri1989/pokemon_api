<?php

declare(strict_types=1);

namespace App\Validator\Constraints;

use Symfony\Component\Validator\Constraint;

#[\Attribute]
/**
 * @Annotation
 */
class BagOwnerConstraint extends Constraint
{
    public string $message = "This item does not belong to the connected trainer.";
}