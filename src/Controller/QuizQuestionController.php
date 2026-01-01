<?php

namespace App\Controller;

use App\Entity\Quiz;
use App\Entity\QuizQuestion;
use App\Form\QuizQuestionType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/quiz-question')]
#[IsGranted(new \Symfony\Component\ExpressionLanguage\Expression("is_granted('ROLE_TEACHER') or is_granted('ROLE_ADMIN')"))]
final class QuizQuestionController extends AbstractController
{
    #[Route('/{quizId}/new', name: 'app_quiz_question_new', methods: ['GET', 'POST'])]
    public function new(Request $request, Quiz $quiz = null, EntityManagerInterface $em): Response
    {
        if (!$quiz) {
            $quizId = $request->attributes->get('quizId');
            $quiz = $em->getRepository(Quiz::class)->find($quizId);
        }

        if (!$quiz) {
            throw $this->createNotFoundException('Quiz not found');
        }

        $question = new QuizQuestion();
        $question->setQuiz($quiz);
        
        $form = $this->createForm(QuizQuestionType::class, $question);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em->persist($question);
            $em->flush();

            $this->addFlash('success', 'Question added successfully!');
            return $this->redirectToRoute('app_quiz_edit', ['id' => $quiz->getId()]);
        }

        return $this->render('quiz_question/new.html.twig', [
            'form' => $form,
            'quiz' => $quiz,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_quiz_question_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, QuizQuestion $question, EntityManagerInterface $em): Response
    {
        $form = $this->createForm(QuizQuestionType::class, $question);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em->flush();

            $this->addFlash('success', 'Question updated successfully!');
            return $this->redirectToRoute('app_quiz_edit', ['id' => $question->getQuiz()->getId()]);
        }

        return $this->render('quiz_question/edit.html.twig', [
            'form' => $form,
            'question' => $question,
            'quiz' => $question->getQuiz(),
        ]);
    }

    #[Route('/{id}/delete', name: 'app_quiz_question_delete', methods: ['POST'])]
    public function delete(Request $request, QuizQuestion $question, EntityManagerInterface $em): Response
    {
        if ($this->isCsrfTokenValid('delete' . $question->getId(), $request->request->get('_token'))) {
            $quizId = $question->getQuiz()->getId();
            $em->remove($question);
            $em->flush();

            $this->addFlash('success', 'Question deleted successfully!');
        }

        return $this->redirectToRoute('app_quiz_edit', ['id' => $quizId]);
    }
}
