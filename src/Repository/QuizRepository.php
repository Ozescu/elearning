<?php

namespace App\Repository;

use App\Entity\Quiz;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

class QuizRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Quiz::class);
    }

    public function findByCourse($course)
    {
        return $this->createQueryBuilder('q')
            ->where('q.course = :course')
            ->setParameter('course', $course)
            ->orderBy('q.created_at', 'DESC')
            ->getQuery()
            ->getResult();
    }
}
