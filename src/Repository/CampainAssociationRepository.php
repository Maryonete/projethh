<?php

namespace App\Repository;

use App\Entity\Association;
use App\Entity\CampainAssociation;
use App\Entity\Campains;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<CampainAssociation>
 *
 * @method CampainAssociation|null find($id, $lockMode = null, $lockVersion = null)
 * @method CampainAssociation|null findOneBy(array $criteria, array $orderBy = null)
 * @method CampainAssociation[]    findAll()
 * @method CampainAssociation[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class CampainAssociationRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, CampainAssociation::class);
    }
    public function findByCampains(Campains $campain): array
    {
        return $this->createQueryBuilder('ca')
            ->join('ca.campains', 'c') // Join avec Campains
            ->andWhere('ca.campains = :val')
            ->setParameter('val', $campain->getId())
            ->getQuery()
            ->getResult();
    }
    public function findOneByCampainsAndAsso(Campains $campain, Association $asso)
    {
        return $this->createQueryBuilder('ca')
            ->join('ca.campains', 'c') // Join avec Campains
            ->join('ca.association', 'a')
            ->andWhere('ca.campains = :val')
            ->andWhere('a.id = :asso')
            ->setParameter('val', $campain->getId())
            ->setParameter('asso', $asso->getId())
            ->getQuery()
            ->getOneOrNullResult();
    }
    public function getCountsForCampain(int $campainId): array
    {
        return $this->createQueryBuilder('ca')
            ->select(
                'COUNT(ca.id) as sentEmailsCount',
                'SUM(CASE WHEN ca.statut = :updated THEN 1 ELSE 0 END) as updatedCount',
                'SUM(CASE WHEN ca.statut = :declined THEN 1 ELSE 0 END) as declinedCount',
                'SUM(CASE WHEN ca.statut = :finish THEN 1 ELSE 0 END) as finishedCount'
            )
            ->where('ca.campains = :campainId')
            ->setParameter('campainId', $campainId)
            ->setParameter('updated', 'updated')
            ->setParameter('declined', 'declined')
            ->setParameter('finish', 'finish')
            ->getQuery()
            ->getSingleResult();
    }
}
