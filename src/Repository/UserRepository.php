<?php

namespace App\Repository;

use App\Entity\User;
use Doctrine\ORM\NoResultException;
use Doctrine\Persistence\ManagerRegistry;
use Doctrine\ORM\NonUniqueResultException;
use Symfony\Component\Security\Core\User\PasswordUpgraderInterface;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Component\Security\Core\Exception\UnsupportedUserException;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;

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
    public function findAllUserPresident($currentAssociationId)
    {
        $req = $this->createQueryBuilder('u')
            ->leftJoin('u.president', 'p')
            ->leftJoin('p.association', 'a');

        if ($currentAssociationId) {
            $req->where('p.association IS NOT NULL AND a.id <> :currentAssociationId')
                ->setParameter('currentAssociationId', $currentAssociationId);
        } else {
            $req->where('p.association IS NOT NULL');
        }

        return $req->getQuery()->getResult();
    }

    /**
     * lister User non president d'une asso ou president de l'asso passée en param
     */
    public function findAllUserNonPresident($currentAssociationId)
    {
        $presidentUsers = $this->findAllUserPresident($currentAssociationId);

        $qb = $this->createQueryBuilder('u')
            ->andWhere('u NOT IN (:presidentUsers)')
            ->setParameter('presidentUsers', $presidentUsers)
            ->andWhere('u.roles NOT LIKE :adminRole')
            ->setParameter('adminRole', '%ROLE_ADMIN%')
            ->orderBy('u.lastname', 'ASC')
            ->addOrderBy('u.firstname', 'ASC');

        return $qb->getQuery()
            ->getResult();
    }


    public function findAllUserReferent($currentAssociationId)
    {
        $req =  $this->createQueryBuilder('u')
            ->leftJoin('App\Entity\Referent', 'p', 'WITH', 'u.id = p.user');

        if ($currentAssociationId) {
            $req->where('p.association IS NOT NULL AND p.association <> :currentAssociationId')
                ->setParameter('currentAssociationId', $currentAssociationId);
        } else {
            $req->where('p.association IS NOT NULL');
        }

        return $req->getQuery()->getResult();
    }
    /**
     * lister User non referent d'une asso ou referent de l'asso passée en param
     */
    public function findAllUserNonReferent($currentAssociationId)
    {
        $referentUsers = $this->findAllUserReferent($currentAssociationId);

        $qb = $this->createQueryBuilder('u')
            ->andWhere('u NOT IN (:referentUsers)')
            ->setParameter('referentUsers', $referentUsers)
            ->andWhere('u.roles NOT LIKE :adminRole')
            ->setParameter('adminRole', '%ROLE_ADMIN%')
            ->orderBy('u.lastname', 'ASC')
            ->addOrderBy('u.firstname', 'ASC');

        return $qb->getQuery()
            ->getResult();
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

    /**
     * @param string $role
     * @return User[] Returns an array of User objects
     */
    public function findOneByRole(string $role): ?User
    {
        try {
            return $this->createQueryBuilder('u')
                ->andWhere('u.roles LIKE :role')
                ->setParameter('role', '%"' . $role . '"%')
                ->getQuery()
                ->getOneOrNullResult();
        } catch (NoResultException | NonUniqueResultException $e) {
            return null;
        }
    }
}
