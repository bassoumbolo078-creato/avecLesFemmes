<?php

namespace App\Repository;

use App\Entity\Amende;
use App\Entity\Campagne;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Amende>
 */
class AmendeRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Amende::class);
    }

    /**
     * Total des amendes pour chaque séance d'une campagne.
     *
     * @return array<int, int> id de la séance => total des amendes
     */
    public function totauxParSeance(Campagne $campagne): array
    {
        $lignes = $this->createQueryBuilder('a')
            ->select('IDENTITY(a.seance) AS seanceId, SUM(a.montant) AS total')
            ->join('a.seance', 's')
            ->andWhere('s.campagne = :campagne')
            ->setParameter('campagne', $campagne)
            ->groupBy('a.seance')
            ->getQuery()
            ->getArrayResult();

        $totaux = [];
        foreach ($lignes as $ligne) {
            $totaux[(int) $ligne['seanceId']] = (int) $ligne['total'];
        }

        return $totaux;
    }
}