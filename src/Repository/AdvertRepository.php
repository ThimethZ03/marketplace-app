<?php

namespace App\Repository;

use App\Entity\Advert;
use App\Entity\Category;
use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

class AdvertRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Advert::class);
    }

    public function findAllPublic()
    {
        return $this->createQueryBuilder('a')
            ->leftJoin('a.user', 'u')
            ->leftJoin('a.category', 'c')
            ->addSelect('u', 'c')
            ->orderBy('a.createdAt', 'DESC');
    }

    public function findByUser(User $user)
    {
        return $this->createQueryBuilder('a')
            ->leftJoin('a.category', 'c')
            ->addSelect('c')
            ->where('a.user = :user')
            ->setParameter('user', $user)
            ->orderBy('a.createdAt', 'DESC')
            ->getQuery()
            ->getResult();
    }

    public function findByCategory(Category $category)
    {
        return $this->createQueryBuilder('a')
            ->leftJoin('a.user', 'u')
            ->addSelect('u')
            ->where('a.category = :category')
            ->setParameter('category', $category)
            ->orderBy('a.createdAt', 'DESC');
    }

    public function searchAdverts(string $query, ?Category $category = null)
    {
        $qb = $this->createQueryBuilder('a')
            ->leftJoin('a.user', 'u')
            ->leftJoin('a.category', 'c')
            ->addSelect('u', 'c')
            ->where('a.title LIKE :query OR a.description LIKE :query OR u.firstName LIKE :query OR u.lastName LIKE :query')
            ->setParameter('query', '%' . $query . '%');

        if ($category) {
            $qb->andWhere('a.category = :category')
               ->setParameter('category', $category);
        }

        return $qb->orderBy('a.createdAt', 'DESC');
    }

    public function countAdverts(): int
    {
        return $this->createQueryBuilder('a')
            ->select('COUNT(a.id)')
            ->getQuery()
            ->getSingleScalarResult();
    }
}
