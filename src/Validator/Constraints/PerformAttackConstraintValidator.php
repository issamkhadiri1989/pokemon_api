<?php

declare(strict_types=1);

namespace App\Validator\Constraints;

use App\ApiResource\Model\Attack;
use App\Entity\Ability;
use App\Entity\Trainer;
use App\Service\Duel\Fight;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;
use Symfony\Component\Validator\Exception\UnexpectedValueException;

/**
 * This class validates the attack process.
 */
class PerformAttackConstraintValidator extends ConstraintValidator
{
    public function __construct(private readonly Security $security, private readonly Fight $fightManager)
    {
    }

    public function validate(mixed $value, Constraint $constraint)
    {
        if (!$constraint instanceof PerformAttackConstraint) {
            throw new UnexpectedTypeException($constraint, PerformAttackConstraint::class);
        }

        if (!$value instanceof Attack) {
            throw new UnexpectedValueException($value, Attack::class);
        }

        // perform validation.
        /** @var Trainer $trainer */
        $trainer = $this->security->getUser();

        $round = $value->getRound();

        // check that the current user is either an opponent or a challenger.
        if ($round->getDuel()->getOpponent() !== $trainer && $round->getDuel()->getChallenger() !== $trainer) {
            $this->context->buildViolation($constraint->trainerNotAllowedMessage)
                ->atPath('round')
                ->addViolation();
        }

        // check that the selected Pokémon is the correct one
        $usedPokemon = $value->getPokemon();
        if ($this->fightManager->trainerHasChosenPokemon($trainer, $usedPokemon, $round) === false) {
            $this->context->buildViolation($constraint->wrongPokemonMessage)
                ->atPath('pokemon')
                ->addViolation();
        }

        // check that the Pokémon can perform the selected move
        $pokemonAbilities = $this->fightManager->selectPokemon($round)?->getAbilities();
        if (null === $pokemonAbilities || ($pokemonAbilities->filter(fn(Ability $item) => $item === $value->getMove())->count() === 0)) {
            $this->context->buildViolation($constraint->moveUnsupportedMessage)
                ->atPath('move')
                ->addViolation();
        }
    }
}
