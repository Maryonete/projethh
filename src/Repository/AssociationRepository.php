<?php

namespace App\Repository;

use App\Entity\Association;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Association>
 *
 * @method Association|null find($id, $lockMode = null, $lockVersion = null)
 * @method Association|null findOneBy(array $criteria, array $orderBy = null)
 * @method Association[]    findAll()
 * @method Association[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class AssociationRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Association::class);
    }
    public function findAllAssociationReferentEmails(): array
    {
        $query = $this->createQueryBuilder('a')
            ->select('a.id, u.email')
            ->join('a.referent', 'r')
            ->join('r.user', 'u')
            ->getQuery();

        $results = $query->getResult();

        $emailsReferents = [];

        foreach ($results as $result) {
            $emailsReferents[$result['id']] = $result['email'];
        }
        return $emailsReferents;
    }
    public function findAllAssociationPresidentEmail(): array
    {
        $query = $this->createQueryBuilder('a')
            ->select('a.id, u.email')
            ->join('a.president', 'p')
            ->join('p.user', 'u')
            ->getQuery();

        $results = $query->getResult();

        $emailsPresidents = [];

        foreach ($results as $result) {
            $emailsPresidents[$result['id']] = $result['email'];
        }
        return $emailsPresidents;
    }
    /**
     * Retourne le nombre d'association enregistrée
     *
     * @return integer
     */
    public function countAssociations(): int
    {
        return $this->createQueryBuilder('a')
            ->select('COUNT(a.id)')
            ->getQuery()
            ->getSingleScalarResult();
    }
    //    /**
    //     * @return Association[] Returns an array of Association objects
    //     */
    //    public function findByExampleField($value): array
    //    {
    //        return $this->createQueryBuilder('a')
    //            ->andWhere('a.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->orderBy('a.id', 'ASC')
    //            ->setMaxResults(10)
    //            ->getQuery()
    //            ->getResult()
    //        ;
    //    }

    //    public function findOneBySomeField($value): ?Association
    //    {
    //        return $this->createQueryBuilder('a')
    //            ->andWhere('a.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }
}
