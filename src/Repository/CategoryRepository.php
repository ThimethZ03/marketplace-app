<?php

namespace App\Repository;

use App\Entity\Category;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

class CategoryRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Category::class);
    }

    /**
     * Returns simple arrays: id, name, slug, icon, advertCount
     */
    public function findAllWithAdvertCount(): array
    {
        return $this->createQueryBuilder('c')
            ->select('c.id', 'c.name', 'c.slug', 'c.icon', 'COUNT(a.id) AS advertCount')
            ->leftJoin('c.adverts', 'a')
            ->groupBy('c.id')
            ->orderBy('c.name', 'ASC')
            ->getQuery()
            ->getScalarResult();
    }
}
