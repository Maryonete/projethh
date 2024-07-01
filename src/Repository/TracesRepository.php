<?php

namespace App\Repository;

use App\Entity\Traces;
use Doctrine\Persistence\ManagerRegistry;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;

/**
 * @extends ServiceEntityRepository<Traces>
 *
 * @method Traces|null find($id, $lockMode = null, $lockVersion = null)
 * @method Traces|null findOneBy(array $criteria, array $orderBy = null)
 * @method Traces[]    findAll()
 * @method Traces[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class TracesRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Traces::class);
    }
}
