<?php

namespace App\Repository;

use App\Entity\President;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<President>
 *
 * @method President|null find($id, $lockMode = null, $lockVersion = null)
 * @method President|null findOneBy(array $criteria, array $orderBy = null)
 * @method President[]    findAll()
 * @method President[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class PresidentRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, President::class);
    }
    /**
     * Trouve un président par son email.
     *
     * @param string $email L'email du président à rechercher.
     * @return President|null Le président trouvé ou null s'il n'existe pas.
     */
    public function findOneByEmail(string $email): ?President
    {
        return $this->createQueryBuilder('p')
            ->join('p.user', 'u') // Supposant qu'il y a une relation user dans l'entité président
            ->andWhere('u.email = :email')
            ->setParameter('email', $email)
            ->getQuery()
            ->getOneOrNullResult();
    }
    //    /**
    //     * @return President[] Returns an array of President objects
    //     */
    //    public function findByExampleField($value): array
    //    {
    //        return $this->createQueryBuilder('p')
    //            ->andWhere('p.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->orderBy('p.id', 'ASC')
    //            ->setMaxResults(10)
    //            ->getQuery()
    //            ->getResult()
    //        ;
    //    }

    //    public function findOneBySomeField($value): ?President
    //    {
    //        return $this->createQueryBuilder('p')
    //            ->andWhere('p.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }
}
