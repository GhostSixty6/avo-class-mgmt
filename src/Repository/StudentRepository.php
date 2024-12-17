<?php

namespace App\Repository;

use App\Entity\Student;
use App\Entity\ClassRoom;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Student>
 *
 * @method Student|null find($id, $lockMode = null, $lockVersion = null)
 * @method Student|null findOneBy(array $criteria, array $orderBy = null)
 * @method Student[]    findAll()
 * @method Student[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class StudentRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Student::class);
    }

    /**
     * Finds all active Student objects
     * @return Student[] Returns an array of Student objects
     */
    public function findAll(): array
    {
        $entityManager = $this->getEntityManager();
        $query = $entityManager->createQuery("SELECT s FROM App\Entity\Student s WHERE s.status = 1");
        return $query->getResult();
    }

    /**
     * Finds Student objects that are part of a classRoom
     * @return Student[] Returns an array of Student objects
     */
    public function findByClassRooms(ClassRoom $classRoom): array
    {
        $entityManager = $this->getEntityManager();
        $query = $entityManager->createQuery("SELECT s FROM App\Entity\Student s JOIN s.classRooms c WHERE s.status = 1 AND c.id = :id")->setParameter('id', $classRoom->getId());
        return $query->getResult();
    }
}
