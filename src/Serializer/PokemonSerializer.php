<?php

declare(strict_types=1);

namespace App\Serializer;

use Symfony\Component\Serializer\Encoder\CsvEncoder;
use Symfony\Component\Serializer\Normalizer\ArrayDenormalizer;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Serializer\Serializer;
use Symfony\Component\Serializer\SerializerInterface;

class PokemonSerializer implements SerializerInterface
{
    public function serialize(mixed $data, string $format, array $context = []): string
    {
        $serializer = new Serializer([new ObjectNormalizer(), new ArrayDenormalizer()], [new CsvEncoder()]);

        return $serializer->serialize($data, 'csv', $context);
    }

    public function deserialize(mixed $data, string $type, string $format, array $context = []): mixed
    {
        $serializer = new Serializer([new ObjectNormalizer(), new ArrayDenormalizer()], [new CsvEncoder()]);

        return $serializer->deserialize($data, $type, 'csv', ['csv_delimiter' => ';', 'no_headers' => false]);
    }
}
