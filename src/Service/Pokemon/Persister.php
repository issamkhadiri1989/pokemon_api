<?php

declare(strict_types=1);

namespace App\Service\Pokemon;

use App\Exception\CategoryNotFoundException;
use App\Model\Pokemon;
use App\Serializer\PokemonSerializer;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Serializer\Normalizer\AbstractObjectNormalizer;

class Persister
{
    public function __construct(
        private readonly PokemonSerializer $serializer,
        private readonly string $source,
        private readonly InstanceManager $manager,
        private readonly EntityManagerInterface $objectManager
    ) {
    }

    /**
     * Saves Pokémon catalog to the database.
     *
     * @return void
     *
     * @throws CategoryNotFoundException
     */
    public function saveCatalog(): void
    {
        $pokemon = $this->buildCatalog();

        $this->doPersistPokemonInstances($pokemon);

        $this->doPersistEvolutions($pokemon);
    }

    /**
     * Builds and gets the catalog of Pokemon.
     *
     * @return Pokemon[]
     */
    private function buildCatalog(): array
    {
        $content = \file_get_contents($this->source . '/pokemon.csv');

        return $this->serializer->deserialize($content, Pokemon::class . '[]', 'csv', [
            AbstractObjectNormalizer::DISABLE_TYPE_ENFORCEMENT => true,
        ]);
    }

    /**
     * @param Pokemon[] $pokemon
     *
     * @return void
     *
     * @throws CategoryNotFoundException
     */
    private function doPersistPokemonInstances(array $pokemon): void
    {
        foreach ($pokemon as $item) {
            $entity = $this->manager->entityBuilder($item);
            $this->objectManager->persist($entity);
        }
        $this->objectManager->flush();

    }

    /**
     * @param Pokemon[] $pokemon
     *
     * @return void
     */
    private function doPersistEvolutions(array $pokemon): void
    {
        foreach ($pokemon as $item) {
            // find the next evolution
            $pokemon = $this->manager->getPokemonWithCode($item->getNextEvolution());
            if (null !== $pokemon) {
                // find the current pokemon
                $entity = $this->manager->getPokemonWithCode($item->getCode());
                // set the next evolution
                $entity->setNextEvolution($pokemon);
                // find the appropriate level
                $this->objectManager->persist($pokemon);
            }
        }

        $this->objectManager->flush();
    }
}
