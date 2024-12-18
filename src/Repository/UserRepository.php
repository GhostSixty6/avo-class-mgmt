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
 * @implements PasswordUpgraderInterface<User>
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
     * Finds all User objects
     * @return User[] Returns an array of User objects
     */
    public function findUsers(): ?array
    {
        $qb = $this->createQueryBuilder('u');
        $qb->select('u');

        return $qb->getQuery()->getResult();
    }

    /**
     * Find all User objects with a specific role
     * @return User[] Returns an array of User objects
     */
    public function findByRole($role): ?array
    {
        $qb = $this->createQueryBuilder('u');
        $qb->select('u')
            ->where('u.roles LIKE :roles')
            ->setParameter('roles', '%"' . $role . '"%');

        return $qb->getQuery()->getResult();
    }

    /**
     * Find all User objects that do not have a specific role
     * @return User[] Returns an array of User objects
     */
    public function findByRoleNot($role): ?array
    {
        $qb = $this->createQueryBuilder('u');
        $qb->select('u')
            ->where('u.roles NOT LIKE :roles')
            ->setParameter('roles', '%"' . $role . '"%');

        return $qb->getQuery()->getResult();
    }

    /**
     * Counts the amount of User objects with a specific role
     * @return int[] Returns an int with the amount of users with role
     */
    public function countByRole($role): ?int
    {
        return count($this->findByRole($role));
    }

    /**
     * Counts the amount of User objects that do not have a specific role
     * @return int[] Returns an int with the amount of users with role
     */
    public function countByRoleNot($role): ?int
    {
        return count($this->findByRoleNot($role));
    }
}
