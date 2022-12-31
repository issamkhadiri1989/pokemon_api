<?php

declare(strict_types=1);

namespace App\Validator\Constraints;

use App\Entity\BagItem;
use App\Entity\Trainer;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;
use Symfony\Component\Validator\Exception\UnexpectedValueException;

/**
 * This validator checks that the current Pokeball belongs to the current trainer.
 */
class BagOwnerConstraintValidator extends ConstraintValidator
{
    public function __construct(private readonly Security $security)
    {
    }

    public function validate(mixed $value, Constraint $constraint)
    {
        if (!$constraint instanceof BagOwnerConstraint) {
            throw new UnexpectedTypeException($constraint, BagOwnerConstraint::class);
        }

        if (null === $value || '' === $value) {
            return;
        }

        if (!$value instanceof BagItem) {
            throw new UnexpectedValueException($value, BagItem::class);
        }

        /** @var Trainer $user */
        $user = $this->security->getUser();

        if ($value->getBag()->getTrainer() !== $user) {
            $this->context
                ->buildViolation($constraint->message)
                ->addViolation();
        }
    }
}