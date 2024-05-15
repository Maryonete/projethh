<?php

namespace App\Repository;

use App\Entity\History;
use App\Entity\Referent;
use App\Entity\President;
use Doctrine\Persistence\ManagerRegistry;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;

/**
 * @extends ServiceEntityRepository<History>
 *
 * @method History|null find($id, $lockMode = null, $lockVersion = null)
 * @method History|null findOneBy(array $criteria, array $orderBy = null)
 * @method History[]    findAll()
 * @method History[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class HistoryRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, History::class);
    }
    public function findReferentByUser($user)
    {

        $entityManager = $this->getEntityManager();
        $referentRepository = $entityManager->getRepository(Referent::class);

        $referents = $referentRepository->findByUser($user);

        if (empty($referents)) {
            // Si le référent n'est trouvé pour l'utilisateur, retourner un tableau vide
            return [];
        }

        $query = $this->createQueryBuilder('h');


        // Check if referentIds is not empty before adding the condition
        $referentIds = array_map(function ($referent) {
            return $referent->getId();
        }, $referents);

        return $query->where($query->expr()->andX(
            $query->expr()->in('h.row_id', $referentIds),
            $query->expr()->eq('h.table_name', ':referent_entity')
        ))->setParameter('referent_entity', 'App\Entity\Referent')
            ->getQuery()->getResult();
    }
    public function findPresidentByUser($user)
    {

        $entityManager = $this->getEntityManager();
        $presidentRepository = $entityManager->getRepository(President::class);
        $referentRepository = $entityManager->getRepository(Referent::class);

        $presidents = $presidentRepository->findByUser($user);
        $referents = $referentRepository->findByUser($user);

        if (empty($presidents) && empty($referents)) {
            // Si ni président ni référent n'est trouvé pour l'utilisateur, retourner un tableau vide
            return [];
        }

        $query = $this->createQueryBuilder('h');

        // Check if presidentIds is not empty before adding the condition

        if (!empty($presidents)) {
            $presidentIds = array_map(function ($president) {
                return $president->getId();
            }, $presidents);

            $query->andWhere($query->expr()->andX(
                $query->expr()->in('h.row_id', $presidentIds),
                $query->expr()->eq('h.table_name', ':president_entity')

            ))->setParameter('president_entity', 'App\Entity\President');
        }

        // Check if referentIds is not empty before adding the condition
        if (!empty($referents)) {
            $referentIds = array_map(function ($referent) {
                return $referent->getId();
            }, $referents);

            $query->orWhere($query->expr()->andX(
                $query->expr()->in('h.row_id', $referentIds),
                $query->expr()->eq('h.table_name', ':referent_entity')
            ))->setParameter('referent_entity', 'App\Entity\Referent');
        }

        // Uncomment this line to see the generated query
        dump($query->getQuery());

        return $query->getQuery()->getResult();
    }
}
