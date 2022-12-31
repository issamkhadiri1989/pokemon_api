<?php

declare(strict_types=1);

namespace App\Model;

class Evolution
{
    private ?string $pokemonCode = null;

    private ?int $evolutionLevel = null;

    /**
     * @return string|null
     */
    public function getPokemonCode(): ?string
    {
        return $this->pokemonCode;
    }

    /**
     * @param string|null $pokemonCode
     */
    public function setPokemonCode(?string $pokemonCode): void
    {
        $this->pokemonCode = $pokemonCode;
    }

    /**
     * @return int|null
     */
    public function getEvolutionLevel(): ?int
    {
        return $this->evolutionLevel;
    }

    /**
     * @param int|null $evolutionLevel
     */
    public function setEvolutionLevel(?int $evolutionLevel): void
    {
        $this->evolutionLevel = $evolutionLevel;
    }
}
