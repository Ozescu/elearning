<?php

namespace App\Repository;

use App\Entity\StudentLesson;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

class StudentLessonRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, StudentLesson::class);
    }

    public function findByStudentAndLesson($student, $lesson)
    {
        return $this->findOneBy(['student' => $student, 'lesson' => $lesson]);
    }
}
