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
    /** retourne un tableau de tous les mails des referents de asso */
    public function findAllAssociationReferentEmails(array $associations = []): array
    {
        $queryBuilder = $this->createQueryBuilder('a')
            ->select('a.id, u.email')
            ->join('a.referent', 'r')
            ->join('r.user', 'u')
            ->where('a.status = :status')
            ->setParameter('status', 'active');

        if (!empty($associations)) {
            // Extraire les identifiants des associations
            $associationIds = array_column($associations, 'id');

            $queryBuilder
                ->andWhere('r.association IN (:associations)')
                ->setParameter('associations', $associationIds);
        }

        $query = $queryBuilder->getQuery();

        $results = $query->getResult();

        $emailsReferents = [];

        foreach ($results as $result) {
            $emailsReferents[$result['id']] = $result['email'];
        }

        return $emailsReferents;
    }
    /** retourne un tableau de tous les mails des presidents de asso */
    public function findAllAssociationPresidentEmail(array $associations = []): array
    {
        $queryBuilder = $this->createQueryBuilder('a')
            ->select('a.id, u.email')
            ->join('a.president', 'p')
            ->join('p.user', 'u')
            ->where('a.status = :status')
            ->setParameter('status', 'active');
        if (!empty($associations)) {
            $associationIds = array_column($associations, 'id');
            $queryBuilder
                ->andWhere('p.association IN (:associations)')
                ->setParameter('associations', $associationIds);
        }

        $query = $queryBuilder->getQuery();


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
            ->where('a.status = :status')
            ->setParameter('status', 'active')
            ->getQuery()
            ->getSingleScalarResult();
    }
}
