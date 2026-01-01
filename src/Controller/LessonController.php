<?php

namespace App\Controller;

use App\Entity\Lesson;
use App\Entity\Course;
use App\Form\LessonType;
use App\Repository\LessonRepository;
use App\Repository\StudentLessonRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\ExpressionLanguage\Expression;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/lesson')]
#[IsGranted(new Expression("is_granted('ROLE_TEACHER') or is_granted('ROLE_ADMIN')"))]
final class LessonController extends AbstractController
{
    #[Route('/course/{courseId}', name: 'app_lesson_index', methods: ['GET'])]
    public function index(int $courseId, LessonRepository $lessonRepository, EntityManagerInterface $em): Response
    {
        $course = $em->getRepository(Course::class)->find($courseId);
        if (!$course) {
            throw $this->createNotFoundException('Course not found');
        }

        $lessons = $lessonRepository->findByCourse($course);

        return $this->render('lesson/index.html.twig', [
            'course' => $course,
            'lessons' => $lessons,
        ]);
    }

    #[Route('/new/{courseId}', name: 'app_lesson_new', methods: ['GET', 'POST'])]
    public function new(int $courseId, Request $request, EntityManagerInterface $em): Response
    {
        $course = $em->getRepository(Course::class)->find($courseId);
        if (!$course) {
            throw $this->createNotFoundException('Course not found');
        }

        $lesson = new Lesson();
        $lesson->setCourse($course);
        $form = $this->createForm(LessonType::class, $lesson);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // Handle file upload
            $pdfFile = $form->get('pdf_file')->getData();
            if ($pdfFile) {
                $newFilename = uniqid() . '.' . $pdfFile->guessExtension();
                $pdfFile->move($this->getParameter('lessons_directory'), $newFilename);
                $lesson->setPdfFile($newFilename);
            }

            $em->persist($lesson);
            $em->flush();

            $this->addFlash('success', 'Lesson created successfully!');
            return $this->redirectToRoute('app_lesson_index', ['courseId' => $courseId]);
        }

        return $this->render('lesson/new.html.twig', [
            'form' => $form,
            'course' => $course,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_lesson_edit', methods: ['GET', 'POST'])]
    public function edit(Lesson $lesson, Request $request, EntityManagerInterface $em): Response
    {
        $form = $this->createForm(LessonType::class, $lesson);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $pdfFile = $form->get('pdf_file')->getData();
            if ($pdfFile) {
                $newFilename = uniqid() . '.' . $pdfFile->guessExtension();
                $pdfFile->move($this->getParameter('lessons_directory'), $newFilename);
                $lesson->setPdfFile($newFilename);
            }

            $em->flush();
            $this->addFlash('success', 'Lesson updated successfully!');
            return $this->redirectToRoute('app_lesson_index', ['courseId' => $lesson->getCourse()->getId()]);
        }

        return $this->render('lesson/edit.html.twig', [
            'form' => $form,
            'lesson' => $lesson,
        ]);
    }

    #[Route('/{id}', name: 'app_lesson_delete', methods: ['POST'])]
    public function delete(Lesson $lesson, Request $request, EntityManagerInterface $em): Response
    {
        if ($this->isCsrfTokenValid('delete' . $lesson->getId(), $request->getPayload()->getString('_token'))) {
            $courseId = $lesson->getCourse()->getId();
            $em->remove($lesson);
            $em->flush();
            $this->addFlash('success', 'Lesson deleted successfully!');
            return $this->redirectToRoute('app_lesson_index', ['courseId' => $courseId]);
        }

        return $this->redirectToRoute('app_lesson_index', ['courseId' => $lesson->getCourse()->getId()]);
    }
}
