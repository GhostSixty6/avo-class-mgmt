<?php

namespace App\Repository;

use App\Entity\ClassRoom;
use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<ClassRoom>
 *
 * @method ClassRoom|null find($id, $lockMode = null, $lockVersion = null)
 * @method ClassRoom|null findOneBy(array $criteria, array $orderBy = null)
 * @method ClassRoom[]    findAll()
 * @method ClassRoom[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ClassRoomRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, ClassRoom::class);
    }

    /**
     * Finds all ClassRoom objects
     * @return ClassRoom[] Returns an array of ClassRoom objects
     */
    public function findAll(): array
    {
        return $this->createQueryBuilder('c')
            ->orderBy('c.id', 'ASC')
            ->andWhere('c.status = 1')
            ->getQuery()
            ->getResult()
        ;
    }

    /**
     * Finds ClassRoom objects that the current user is a teacher of
     * @return ClassRoom[] Returns an array of ClassRoom objects
     */
    public function findByTeachers(User $user): array
    {
        $entityManager = $this->getEntityManager();

        $query = $entityManager->createQuery("SELECT c FROM App\Entity\ClassRoom c JOIN c.teachers u WHERE c.status = 1 AND u.id = :uid")->setParameter('uid', $user->getId());

        return $query->getResult();
    }
}
