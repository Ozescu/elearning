<?php

namespace App\Repository;

use App\Entity\Lesson;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

class LessonRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Lesson::class);
    }

    public function findByCourse($course)
    {
        return $this->createQueryBuilder('l')
            ->where('l.course = :course')
            ->setParameter('course', $course)
            ->orderBy('l.created_at', 'DESC')
            ->getQuery()
            ->getResult();
    }
}
