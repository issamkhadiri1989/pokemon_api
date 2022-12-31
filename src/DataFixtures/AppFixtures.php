<?php

namespace App\DataFixtures;

use App\Entity\Category;
use App\Entity\Trainer;
use App\Serializer\PokemonSerializer;
use App\Serializer\TrainerSerializer;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Serializer\Encoder\CsvEncoder;

class AppFixtures extends Fixture
{
    private TrainerSerializer $serializer;
    private UserPasswordHasherInterface $hasher;
    private PokemonSerializer $pokemonSerializer;

    public function __construct(TrainerSerializer $serializer, UserPasswordHasherInterface $hasher, PokemonSerializer $pokemonSerializer)
    {
        $this->serializer = $serializer;
        $this->hasher = $hasher;
        $this->pokemonSerializer = $pokemonSerializer;
    }

    public function load(ObjectManager $manager): void
    {
        // trainers
        /** @var Trainer[] $trainers */
        $trainers = $this->serializer->deserialize(
            \file_get_contents('/var/www/html/data/trainers.csv'),
            Trainer::class.'[]',
            'csv'
        );

        foreach ($trainers as $trainer) {
            $trainer->setPassword($this->hasher->hashPassword($trainer, '000000'));
            $manager->persist($trainer);
        }

        // categories
        /** @var Category[] $categories */
        $categories = $this->serializer->deserialize(
            \file_get_contents('/var/www/html/data/types.csv'),
            Category::class.'[]',
            'csv'
        );
        \array_walk($categories, fn (Category $item, $index) => $manager->persist($item));

        $manager->flush();
    }
}
