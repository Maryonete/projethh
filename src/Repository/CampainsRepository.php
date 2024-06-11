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

    public function setAllValidToFalseExcept(Campains $campain): void
    {
        $this->createQueryBuilder('c')
            ->update()
            ->set('c.valid', ':valid')
            ->andWhere('c != :campain')
            ->setParameter('valid', false)
            ->setParameter('campain', $campain)
            ->getQuery()
            ->execute();
    }
}
