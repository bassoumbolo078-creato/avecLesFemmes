<?php

namespace App\Repository;

use App\Entity\Campagne;
use App\Entity\ModeCotisation;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<ModeCotisation>
 */
class ModeCotisationRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, ModeCotisation::class);
    }

    /**
     * Nombre de membres affectés à chaque campagne.
     *
     * @return array<int, int> id de la campagne => nombre de membres
     */
    public function compterParCampagne(): array
    {
        $lignes = $this->createQueryBuilder('mc')
            ->select('IDENTITY(mc.campagne) AS campagneId, COUNT(mc.id) AS nombre')
            ->groupBy('mc.campagne')
            ->getQuery()
            ->getArrayResult();

        $nombres = [];
        foreach ($lignes as $ligne) {
            $nombres[(int) $ligne['campagneId']] = (int) $ligne['nombre'];
        }

        return $nombres;
    }

    /**
     * Les affectations d'une campagne, avec leur membre, classées par nom.
     *
     * @return ModeCotisation[]
     */
    public function findByCampagne(Campagne $campagne): array
    {
        return $this->createQueryBuilder('mc')
            ->addSelect('m')
            ->join('mc.membre', 'm')
            ->andWhere('mc.campagne = :campagne')
            ->setParameter('campagne', $campagne)
            ->orderBy('m.nomComplet', 'ASC')
            ->getQuery()
            ->getResult();
    }
}