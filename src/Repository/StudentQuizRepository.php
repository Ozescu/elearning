<?php

namespace App\Repository;

use App\Entity\StudentQuiz;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

class StudentQuizRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, StudentQuiz::class);
    }

    public function findByStudentAndQuiz($student, $quiz)
    {
        return $this->findOneBy(['student' => $student, 'quiz' => $quiz]);
    }
}
