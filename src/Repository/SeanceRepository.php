<?php

namespace App\Repository;

use App\Entity\Campagne;
use App\Entity\Seance;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Seance>
 */
class SeanceRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Seance::class);
    }

    /**
     * Les séances d'une campagne, de la plus ancienne à la plus récente.
     *
     * @return Seance[]
     */
    public function findByCampagne(Campagne $campagne): array
    {
        return $this->createQueryBuilder('s')
            ->andWhere('s.campagne = :campagne')
            ->setParameter('campagne', $campagne)
            ->orderBy('s.dateAt', 'ASC')
            ->getQuery()
            ->getResult();
    }
}