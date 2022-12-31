<?php

declare(strict_types=1);

namespace App\Repository;

use App\Entity\BagItem;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<BagItem>
 *
 * @method BagItem|null find($id, $lockMode = null, $lockVersion = null)
 * @method BagItem|null findOneBy(array $criteria, array $orderBy = null)
 * @method BagItem[]    findAll()
 * @method BagItem[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class BagItemRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, BagItem::class);
    }
}
