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
    public function countSentEmailsForCampain(int $campainId): int
    {
        return $this->createQueryBuilder('ca')
            ->select('COUNT(ca.id)')
            ->where('ca.campains = :campainId')
            // ->andWhere('ca.statut = :statut')
            ->setParameter('campainId', $campainId)
            // ->setParameter('statut', 'send')
            ->getQuery()
            ->getSingleScalarResult();
    }
    //    /**
    //     * @return CampainAssociation[] Returns an array of CampainAssociation objects
    //     */
    //    public function findByExampleField($value): array
    //    {
    //        return $this->createQueryBuilder('c')
    //            ->andWhere('c.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->orderBy('c.id', 'ASC')
    //            ->setMaxResults(10)
    //            ->getQuery()
    //            ->getResult()
    //        ;
    //    }

    //    public function findOneBySomeField($value): ?CampainAssociation
    //    {
    //        return $this->createQueryBuilder('c')
    //            ->andWhere('c.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }
}
