<?php

declare(strict_types=1);

namespace App\Validator\Constraints;

use App\Entity\Trainer;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;
use Symfony\Component\Validator\Exception\UnexpectedValueException;

/**
 * This validator checks that the current trainer the attribute is not the current trainer,
 */
class NotCurrentUserConstraintValidator extends ConstraintValidator
{
    public function __construct(private readonly Security $security)
    {
    }

    public function validate(mixed $value, Constraint $constraint)
    {
        if (!$constraint instanceof NotCurrentUserConstraint) {
            throw new UnexpectedTypeException($constraint, NotCurrentUserConstraint::class);
        }

        if (null === $value || '' === $value) {
            return;
        }

        if (!$value instanceof Trainer) {
            throw new UnexpectedValueException($value, Trainer::class);
        }

        /** @var Trainer $user */
        $user = $this->security->getUser();

        if ($user === $value) {
            $this->context
                ->buildViolation($constraint->message)
                ->addViolation();
        }
    }
}