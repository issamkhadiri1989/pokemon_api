<?php

declare(strict_types=1);

namespace App\Processor;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProcessorInterface;
use App\Entity\BagItem;
use App\Entity\Round;
use App\Entity\Trainer;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\SecurityBundle\Security;

class DuelPokemonSelector implements ProcessorInterface
{
    public function __construct(
        private readonly ProcessorInterface $persistProcessor,
        private readonly Security $security
    ) {

    }

    public function process(mixed $data, Operation $operation, array $uriVariables = [], array $context = [])
    {
        if (!$data instanceof Round) {
            return $data;
        }

        $this->associatePokemonToTrainer($data);

        return $this->persistProcessor->process($data, $operation, $uriVariables, $context);
    }

    private function associatePokemonToTrainer(Round $round): void
    {
        /** @var Trainer $trainer */
        $trainer = $this->security->getUser();
        $duel = $round->getDuel();
        $pokemon = $round->getSelectedPokemon();
//        if ($pokemon->getBag()->getTrainer() === $trainer) {
            if ($duel->getChallenger() === $trainer) {
                $round->setChallengerPokemon($pokemon);
            } elseif ($duel->getOpponent() === $trainer) {
                $round->setOpponentPokemon($pokemon);
            }
//        }
    }
}
