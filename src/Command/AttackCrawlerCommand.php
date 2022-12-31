<?php

declare(strict_types=1);

namespace App\Command;

use App\Service\Pokemon\AttackLoader;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

#[AsCommand(name: "pokemon:attack:load")]
class AttackCrawlerCommand extends Command
{
    private const SINGLE_POKEMON_ATTACKS_LIST = 'https://pokemondb.net/pokedex';

    public function __construct(private AttackLoader $loader)
    {
        parent::__construct();
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $this->loader->loadMovesByPokemon(self::SINGLE_POKEMON_ATTACKS_LIST);

        return Command::SUCCESS;
    }
}