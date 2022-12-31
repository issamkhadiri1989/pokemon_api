<?php

declare(strict_types=1);

namespace App\Service\Pokemon;

use App\Entity\Category;
use App\Entity\Pokemon;
use App\Exception\CategoryNotFoundException;
use App\Model\Pokemon as PokemonModel;
use Doctrine\ORM\EntityManagerInterface;

class InstanceManager
{
    public function __construct(private EntityManagerInterface $manager)
    {
    }

    /**
     * Creates an instance of the Pokemon Entity by the model.
     *
     * @param PokemonModel $model
     *
     * @return Pokemon
     *
     * @throws CategoryNotFoundException
     */
    public function entityBuilder(PokemonModel $model): Pokemon
    {
        $entity = new Pokemon();
        $entity->setCode($model->getCode())
            ->setPicture(\sprintf(
                'https://assets.pokemon.com/assets/cms2/img/pokedex/detail/%s.png',
                \substr($model->getCode(), 1)
            ))
            ->setName($model->getName());
        if (empty($mainType = $model->getMainType()) === false) {
            $mainCategory = $this->retrieveCategory($mainType);
            if (null === $mainCategory) {
                throw new CategoryNotFoundException($mainType);
            }
            $entity->setMainType($mainType)
                ->setMainCategory($mainCategory);
        }

        if (empty($secondType = $model->getSecondType()) === false) {
            $secondaryCategory = $this->retrieveCategory($secondType);
            $entity->setSecondType($secondType)
                ->setSecondaryCategory($secondaryCategory);
        }

        if (empty($model->getEvolutionLevel()) === false) {
            $entity->setEvolutionLevel((int)$model->getEvolutionLevel());
        }

        $entity->setAttack($model->getAttack())
            ->setDefense($model->getDefense())
            ->setSpeed($model->getSpeed())
            ->setHealth($model->getHealthPoint());

        return $entity;
    }

    /**
     * Finds and get the Pokemon instance by its code.
     *
     * @param string $code
     *
     * @return Pokemon|null
     */
    public function getPokemonWithCode(string $code): ?Pokemon
    {
        return $this->manager->getRepository(Pokemon::class)->findOneBy(['code' => $code]);
    }

    /**
     * Retrieves the category of the given Pokemon instance.
     *
     * @param string $category
     *
     * @return Category|null
     */
    private function retrieveCategory(string $category): ?Category
    {
        return $this->manager
            ->getRepository(Category::class)
            ->findOneBy(['name' => $category]);
    }
}