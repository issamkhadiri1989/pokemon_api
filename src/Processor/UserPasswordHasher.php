<?php

declare(strict_types=1);

namespace App\Processor;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProcessorInterface;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

final class UserPasswordHasher implements ProcessorInterface
{
    public function __construct(
        private readonly UserPasswordHasherInterface $hasher,
        private readonly ProcessorInterface $processor
    ) {

    }

    public function process(mixed $data, Operation $operation, array $uriVariables = [], array $context = []): void
    {
        $plainPassword = $data->getPassword();
        $hashedPassword = $this->hasher->hashPassword($data, $plainPassword);
        $data->setPassword($hashedPassword);
        $this->processor->process($data, $operation, $uriVariables, $context);
    }
}
