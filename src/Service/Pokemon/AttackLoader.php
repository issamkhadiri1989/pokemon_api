<?php

declare(strict_types=1);

namespace App\Service\Pokemon;

use App\Entity\Ability;
use App\Entity\Pokemon;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\DomCrawler\Crawler;

class AttackLoader
{
    public function __construct(private readonly EntityManagerInterface $entityManager)
    {

    }

    /**
     * Fetches moves from the url.
     *
     * @param string $sourceUrl
     *
     * @return void
     */
    public function loadMovesByPokemon(string $sourceUrl): void
    {
        /** @var Pokemon[] $catalog */
        $catalog = $this->entityManager->getRepository(Pokemon::class)->findAll();
        \array_walk($catalog, function (Pokemon $entry) use ($sourceUrl) {
            $pageUrl = $this->buildUrl($entry, $sourceUrl);
            $this->doParseSourceForAttack($pageUrl, $entry);
        });
    }

    /**
     * Builds the url to fetch ability with level fpr the given Pokemon instance.
     *
     * @param Pokemon $entity
     * @param string $baseUrl
     *
     * @return string
     */
    private function buildUrl(Pokemon $entity, string $baseUrl): string
    {
        $name = \strtolower($entity->getName());
        $name = match ($name) {
            "nidoran♀" => 'nidoran-f',
            "nidoran♂" => 'nidoran-m',
            "mr. mime" => 'mr-mime',
            "farfetch'd" => str_replace("'", '', $name),
            default => $name
        };

        return $baseUrl.'/'.$name;
    }

    /**
     * Finds and adds ability for the given Pokemon instance.
     *
     * @param string $url
     * @param Pokemon $entity
     *
     * @return void
     */
    private function doParseSourceForAttack(string $url, Pokemon $entity): void
    {
        $content = \file_get_contents($url);
        $crawler = new Crawler($content);
        // load the move instance by the name/slug
        $movesList = $crawler->filter('.grid-col.span-lg-6')
            ->eq(0)
            ->filter('table > tbody')
            ->eq(0)
            ->filter('tr');
        $movesList->each(function (Crawler $item) use ($entity) {
            $level = (int) $item->filter('td.cell-num')->text();
            $attackName = $item->filter('td.cell-name')->text();
            $ability = (new Ability())->setLevel($level)->setMove($attackName)->setPokemon($entity);
            $this->entityManager->persist($ability);
            $entity->addAbility($ability);
        });
        $this->entityManager->flush();
    }
}
