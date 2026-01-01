<?php

namespace App\Controller;

use App\Entity\Lesson;
use App\Entity\StudentLesson;
use App\Repository\StudentLessonRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/student/lesson')]
#[IsGranted('ROLE_STUDENT')]
final class StudentLessonController extends AbstractController
{
    #[Route('/{id}', name: 'app_student_lesson_view', methods: ['GET'])]
    public function view(Lesson $lesson, StudentLessonRepository $studentLessonRepo): Response
    {
        $user = $this->getUser();
        $studentLesson = $studentLessonRepo->findByStudentAndLesson($user, $lesson);

        return $this->render('student/lesson/view.html.twig', [
            'lesson' => $lesson,
            'studentLesson' => $studentLesson,
        ]);
    }

    #[Route('/{id}/mark-complete', name: 'app_student_lesson_complete', methods: ['POST'])]
    public function markComplete(Lesson $lesson, StudentLessonRepository $studentLessonRepo, EntityManagerInterface $em): Response
    {
        $user = $this->getUser();
        $studentLesson = $studentLessonRepo->findByStudentAndLesson($user, $lesson);

        if (!$studentLesson) {
            $studentLesson = new StudentLesson();
            $studentLesson->setStudent($user);
            $studentLesson->setLesson($lesson);
        }

        $studentLesson->setCompleted(true);
        $studentLesson->setCompletedAt(new \DateTime());

        $em->persist($studentLesson);
        $em->flush();

        $this->addFlash('success', 'Lesson marked as completed!');

        return $this->redirectToRoute('app_student_lesson_view', ['id' => $lesson->getId()]);
    }
}
