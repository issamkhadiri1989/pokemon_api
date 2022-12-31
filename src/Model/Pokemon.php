<?php

declare(strict_types=1);

namespace App\Model;

class Pokemon
{
    private ?string $code = null;
    private ?string $name = null;
    private ?string $mainType = null;
    private ?string $secondType = null;
    private ?string $picture = null;
    private ?string $mainCategory = null;
    private ?string $secondaryCategory = null;
    private ?string $nextEvolution = null;
    private mixed $evolutionLevel = null;
    private ?int $healthPoint = null;
    private ?int $attack = null;
    private ?int $defense = null;
    private ?int $speed = null;

    public function getCode(): ?string
    {
        return $this->code;
    }

    public function setCode(?string $code): self
    {
        $this->code = $code;

        return $this;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(?string $name): self
    {
        $this->name = $name;

        return $this;
    }

    public function getMainType(): ?string
    {
        return $this->mainType;
    }

    public function setMainType(?string $mainType): self
    {
        $this->mainType = $mainType;

        return $this;
    }

    public function getSecondType(): ?string
    {
        return $this->secondType;
    }

    public function setSecondType(?string $secondType): self
    {
        $this->secondType = $secondType;

        return $this;
    }

    public function getPicture(): ?string
    {
        return $this->picture;
    }

    public function setPicture(?string $picture): self
    {
        $this->picture = $picture;

        return $this;
    }

    public function getMainCategory(): ?string
    {
        return $this->mainCategory;
    }

    public function setMainCategory(?string $mainCategory): self
    {
        $this->mainCategory = $mainCategory;

        return $this;
    }

    public function getSecondaryCategory(): ?string
    {
        return $this->secondaryCategory;
    }

    public function setSecondaryCategory(?string $secondaryCategory): self
    {
        $this->secondaryCategory = $secondaryCategory;

        return $this;
    }

    public function getNextEvolution(): ?string
    {
        return $this->nextEvolution;
    }

    public function setNextEvolution(?string $nextEvolution): self
    {
        $this->nextEvolution = $nextEvolution;

        return $this;
    }

    /**
     * @return int|null
     */
    public function getEvolutionLevel(): mixed
    {
        return $this->evolutionLevel;
    }

    /**
     * @param int|null $evolutionLevel
     */
    public function setEvolutionLevel(mixed $evolutionLevel): self
    {
        $this->evolutionLevel = $evolutionLevel;

        return $this;
    }

    /**
     * @return int|null
     */
    public function getHealthPoint(): ?int
    {
        return $this->healthPoint;
    }

    /**
     * @param int|null $healthPoint
     *
     * @return Pokemon
     */
    public function setHealthPoint(?int $healthPoint): self
    {
        $this->healthPoint = $healthPoint;

        return $this;
    }

    /**
     * @return int|null
     */
    public function getAttack(): ?int
    {
        return $this->attack;
    }

    /**
     * @param int|null $attack
     *
     * @return Pokemon
     */
    public function setAttack(?int $attack): self
    {
        $this->attack = $attack;

        return $this;
    }

    /**
     * @return int|null
     */
    public function getDefense(): ?int
    {
        return $this->defense;
    }

    /**
     * @param int|null $defense
     *
     * @return Pokemon
     */
    public function setDefense(?int $defense): self
    {
        $this->defense = $defense;

        return $this;
    }

    /**
     * @return int|null
     */
    public function getSpeed(): ?int
    {
        return $this->speed;
    }

    /**
     * @param int|null $speed
     *
     * @return Pokemon
     */
    public function setSpeed(?int $speed): self
    {
        $this->speed = $speed;

        return $this;
    }
}
