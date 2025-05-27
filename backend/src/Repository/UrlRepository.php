<?php

declare(strict_types=1);

namespace App\Repository;

use App\Entity\Url;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

class UrlRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Url::class);
    }

    public function create(Url $url): Url
    {
        $this->getEntityManager()->persist($url);
        $this->getEntityManager()->flush();

        return $url;
    }

    public function findWithCache(int $id): ?Url
    {
        $query = $this->getEntityManager()->createQuery(
            'SELECT u
            FROM '. $this->getEntityName(). ' u
            WHERE u.id = :id',
        )->setParameter('id', $id);

        $query->enableResultCache(60);

        return $query->getOneOrNullResult();
    }
}
