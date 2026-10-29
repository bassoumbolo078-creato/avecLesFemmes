<?php

namespace App\Repository;

use App\Entity\Campagne;
use App\Entity\Cotisation;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Cotisation>
 */
class CotisationRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Cotisation::class);
    }

    /**
     * Total d'épargne collecté et total de caisse sociale, pour chaque séance d'une campagne.
     *
     * @return array<int, array{collecte: int, caisse: int}> id de la séance => totaux
     */
    public function totauxParSeance(Campagne $campagne): array
    {
        $lignes = $this->createQueryBuilder('co')
            ->select('IDENTITY(co.seance) AS seanceId, SUM(co.montant) AS collecte, SUM(co.montantCaisse) AS caisse')
            ->join('co.seance', 's')
            ->andWhere('s.campagne = :campagne')
            ->setParameter('campagne', $campagne)
            ->groupBy('co.seance')
            ->getQuery()
            ->getArrayResult();

        $totaux = [];
        foreach ($lignes as $ligne) {
            $totaux[(int) $ligne['seanceId']] = [
                'collecte' => (int) $ligne['collecte'],
                'caisse' => (int) $ligne['caisse'],
            ];
        }

        return $totaux;
    }
}