<?php

namespace App\Repository;

use App\Entity\Announcement;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

class AnnouncementRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Announcement::class);
    }

    public function findForStudent($student)
    {
        // Get announcements that are global or for groups this student is in
        return $this->createQueryBuilder('a')
            ->leftJoin('a.group', 'g')
            ->leftJoin('App\Entity\StudentGroup', 'sg', 'WITH', 'sg.group = g.id')
            ->where('a.is_global = 1 OR (sg.student = :student AND sg.approved = 1)')
            ->setParameter('student', $student)
            ->orderBy('a.created_at', 'DESC')
            ->getQuery()
            ->getResult();
    }
}
