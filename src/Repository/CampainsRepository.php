<?php

namespace App\Repository;

use App\Entity\Campains;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Campains>
 *
 * @method Campains|null find($id, $lockMode = null, $lockVersion = null)
 * @method Campains|null findOneBy(array $criteria, array $orderBy = null)
 * @method Campains[]    findAll()
 * @method Campains[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class CampainsRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Campains::class);
    }

    //    /**
    //     * @return Campains[] Returns an array of Campains objects
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

    //    public function findOneBySomeField($value): ?Campains
    //    {
    //        return $this->createQueryBuilder('c')
    //            ->andWhere('c.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }
}
