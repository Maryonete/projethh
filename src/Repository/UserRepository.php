<?php

namespace App\Repository;

use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\Security\Core\Exception\UnsupportedUserException;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\PasswordUpgraderInterface;

/**
 * @extends ServiceEntityRepository<User>
 *
 * @method User|null find($id, $lockMode = null, $lockVersion = null)
 * @method User|null findOneBy(array $criteria, array $orderBy = null)
 * @method User[]    findAll()
 * @method User[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class UserRepository extends ServiceEntityRepository implements PasswordUpgraderInterface
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, User::class);
    }
    public function findAllUserPresident()
    {
        return $this->createQueryBuilder('u')
            ->leftJoin('App\Entity\President', 'p', 'WITH', 'u.id = p.user')
            ->where('p.association IS NOT NULL') // Filtrer les présidents
            ->getQuery()
            ->getResult();
    }
    /** 
     * list User non president d'une asso ou president de l'asso passée en param
     */
    public function findAllUserNonPresident($currentAssociationId)
    {
        dump($currentAssociationId);
        $presidentUsers = $this->findAllUserPresident();

        $qb = $this->createQueryBuilder('u')
            ->andWhere('u NOT IN (:presidentUsers)')
            // ->setParameter('currentAssociationId', $currentAssociationId)
            ->setParameter('presidentUsers', $presidentUsers);
        dump($qb->getQuery());
        dump($presidentUsers);
        return $qb->getQuery()
            ->getResult();
        // $qb = $this->createQueryBuilder('u');

        // if ($currentAssociationId) {
        //     $qb->leftJoin(
        //         'App\Entity\President',
        //         'p',
        //         'WITH',
        //         'u.id = p.user
        //         AND (p.association IS NULL OR p.association = :currentAssociationId)'
        //     )
        //         ->setParameter('currentAssociationId', $currentAssociationId);
        // } else {
        //     $qb->leftJoin('App\Entity\President', 'p', 'WITH', 'u.id = p.user.id AND p.association IS NULL');
        // }
        // $qb->orderBy('u.lastname', 'ASC')
        //     ->addOrderBy('u.firstname', 'ASC');
        // 
        // return $qb->getQuery()->getResult();
    }
    public function findAllUserNonReferent($currentAssociationId)
    {
        $qb = $this->createQueryBuilder('u');

        $qb->leftJoin('App\Entity\Referent', 'r', 'WITH', 'u.id = r.user')
            ->where('r.association IS NULL OR r.association = :currentAssociationId')
            ->setParameter('currentAssociationId', $currentAssociationId)
            ->orderBy('u.lastname', 'ASC')
            ->addOrderBy('u.firstname', 'ASC');

        return $qb->getQuery()->getResult();
    }
    /**
     * Used to upgrade (rehash) the user's password automatically over time.
     */
    public function upgradePassword(PasswordAuthenticatedUserInterface $user, string $newHashedPassword): void
    {
        if (!$user instanceof User) {
            throw new UnsupportedUserException(sprintf('Instances of "%s" are not supported.', $user::class));
        }

        $user->setPassword($newHashedPassword);
        $this->getEntityManager()->persist($user);
        $this->getEntityManager()->flush();
    }
}
