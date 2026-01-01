<?php

namespace App\Controller;

use App\Repository\StudentGroupRepository;
use App\Repository\CourseRepository;
use App\Repository\AnnouncementRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

final class StudentController extends AbstractController
{
    #[Route('/student', name: 'app_student')]
    #[IsGranted('ROLE_STUDENT')]
    public function index(StudentGroupRepository $studentGroupRepository, CourseRepository $courseRepository, AnnouncementRepository $announcementRepository): Response
    {
        $user = $this->getUser();
        
        // Find student's group assignments
        $studentGroups = $studentGroupRepository->findBy(['student' => $user, 'approved' => true]);
        
        // Get all courses from the student's assigned groups
        $courses = [];
        foreach ($studentGroups as $sg) {
            $groupCourses = $courseRepository->findBy(['group' => $sg->getGroup()]);
            $courses = array_merge($courses, $groupCourses);
        }

        // Get announcements
        $announcements = $announcementRepository->findForStudent($user);
        
        return $this->render('student/index.html.twig', [
            'studentGroups' => $studentGroups,
            'courses' => $courses,
            'announcements' => $announcements,
        ]);
    }
}

