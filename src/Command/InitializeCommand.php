<?php

declare(strict_types=1);

namespace App\Command;

use App\Service\Pokemon\Persister;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

#[AsCommand(name: "pokemon:initialize")]
class InitializeCommand extends Command
{
    public function __construct(private Persister $persister)
    {
        parent::__construct();
    }

    public function execute(InputInterface $in, OutputInterface $out): int
    {
        $this->persister->saveCatalog();
        $out->writeln('<info>Pokemon catalog has been saved</info>');

        return Command::SUCCESS;
    }
}
