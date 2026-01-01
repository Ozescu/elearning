<?php

namespace App\Controller;

use App\Entity\Quiz;
use App\Entity\Course;
use App\Form\QuizType;
use App\Repository\QuizRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\ExpressionLanguage\Expression;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/quiz')]
#[IsGranted(new Expression("is_granted('ROLE_TEACHER') or is_granted('ROLE_ADMIN')"))]
final class QuizController extends AbstractController
{
    #[Route('/course/{courseId}', name: 'app_quiz_index', methods: ['GET'])]
    public function index(int $courseId, QuizRepository $quizRepository, EntityManagerInterface $em): Response
    {
        $course = $em->getRepository(Course::class)->find($courseId);
        if (!$course) {
            throw $this->createNotFoundException('Course not found');
        }

        $quizzes = $quizRepository->findByCourse($course);

        return $this->render('quiz/index.html.twig', [
            'course' => $course,
            'quizzes' => $quizzes,
        ]);
    }

    #[Route('/new/{courseId}', name: 'app_quiz_new', methods: ['GET', 'POST'])]
    public function new(int $courseId, Request $request, EntityManagerInterface $em): Response
    {
        $course = $em->getRepository(Course::class)->find($courseId);
        if (!$course) {
            throw $this->createNotFoundException('Course not found');
        }

        $quiz = new Quiz();
        $quiz->setCourse($course);
        $form = $this->createForm(QuizType::class, $quiz);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em->persist($quiz);
            $em->flush();

            $this->addFlash('success', 'Quiz created successfully!');
            return $this->redirectToRoute('app_quiz_index', ['courseId' => $courseId]);
        }

        return $this->render('quiz/new.html.twig', [
            'form' => $form,
            'course' => $course,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_quiz_edit', methods: ['GET', 'POST'])]
    public function edit(Quiz $quiz, Request $request, EntityManagerInterface $em): Response
    {
        $form = $this->createForm(QuizType::class, $quiz);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em->flush();
            $this->addFlash('success', 'Quiz updated successfully!');
            return $this->redirectToRoute('app_quiz_index', ['courseId' => $quiz->getCourse()->getId()]);
        }

        return $this->render('quiz/edit.html.twig', [
            'form' => $form,
            'quiz' => $quiz,
        ]);
    }

    #[Route('/{id}', name: 'app_quiz_delete', methods: ['POST'])]
    public function delete(Quiz $quiz, Request $request, EntityManagerInterface $em): Response
    {
        if ($this->isCsrfTokenValid('delete' . $quiz->getId(), $request->getPayload()->getString('_token'))) {
            $courseId = $quiz->getCourse()->getId();
            $em->remove($quiz);
            $em->flush();
            $this->addFlash('success', 'Quiz deleted successfully!');
            return $this->redirectToRoute('app_quiz_index', ['courseId' => $courseId]);
        }

        return $this->redirectToRoute('app_quiz_index', ['courseId' => $quiz->getCourse()->getId()]);
    }
}
