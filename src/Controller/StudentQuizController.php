<?php

namespace App\Controller;

use App\Entity\Quiz;
use App\Entity\StudentQuiz;
use App\Repository\StudentQuizRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/student/quiz')]
#[IsGranted('ROLE_STUDENT')]
final class StudentQuizController extends AbstractController
{
    #[Route('/{id}', name: 'app_student_quiz_view', methods: ['GET'])]
    public function view(Quiz $quiz, StudentQuizRepository $studentQuizRepo): Response
    {
        $user = $this->getUser();
        $studentQuiz = $studentQuizRepo->findByStudentAndQuiz($user, $quiz);

        return $this->render('student/quiz/view.html.twig', [
            'quiz' => $quiz,
            'studentQuiz' => $studentQuiz,
        ]);
    }

    #[Route('/{id}/start', name: 'app_student_quiz_start', methods: ['GET'])]
    public function start(Quiz $quiz): Response
    {
        if ($quiz->isOverdue()) {
            $this->addFlash('error', 'This quiz is overdue and cannot be submitted.');
            return $this->redirectToRoute('app_student_quiz_view', ['id' => $quiz->getId()]);
        }

        return $this->render('student/quiz/take.html.twig', [
            'quiz' => $quiz,
        ]);
    }

    #[Route('/{id}/submit', name: 'app_student_quiz_submit', methods: ['POST'])]
    public function submit(Request $request, Quiz $quiz, StudentQuizRepository $studentQuizRepo, EntityManagerInterface $em): Response
    {
        if ($quiz->isOverdue()) {
            $this->addFlash('error', 'This quiz is overdue and cannot be submitted.');
            return $this->redirectToRoute('app_student_quiz_view', ['id' => $quiz->getId()]);
        }

        $user = $this->getUser();
        $answers = $request->request->all();
        unset($answers['_token']);

        // Calculate score
        $score = 0;
        $totalQuestions = count($quiz->getQuestions());

        foreach ($quiz->getQuestions() as $question) {
            $fieldName = 'question_' . $question->getId();
            if (isset($answers[$fieldName])) {
                $userAnswer = $answers[$fieldName];
                
                // Normalize answer comparison (case-insensitive for short answers)
                $correctAnswer = strtolower(trim($question->getCorrectAnswer()));
                $userAnswerNorm = strtolower(trim($userAnswer));
                
                if ($userAnswerNorm === $correctAnswer) {
                    $score++;
                }
            }
        }

        // Convert to percentage
        $scorePercentage = $totalQuestions > 0 ? ($score / $totalQuestions) * 100 : 0;

        // Save student quiz submission
        $studentQuiz = $studentQuizRepo->findByStudentAndQuiz($user, $quiz);
        if (!$studentQuiz) {
            $studentQuiz = new StudentQuiz();
            $studentQuiz->setStudent($user);
            $studentQuiz->setQuiz($quiz);
        }

        $studentQuiz->setScore($scorePercentage);
        $studentQuiz->setSubmittedAt(new \DateTime());
        $studentQuiz->setAnswers($answers);

        $em->persist($studentQuiz);
        $em->flush();

        $this->addFlash('success', 'Quiz submitted successfully!');

        return $this->redirectToRoute('app_student_quiz_results', ['id' => $quiz->getId()]);
    }

    #[Route('/{id}/results', name: 'app_student_quiz_results', methods: ['GET'])]
    public function results(Quiz $quiz, StudentQuizRepository $studentQuizRepo): Response
    {
        $user = $this->getUser();
        $studentQuiz = $studentQuizRepo->findByStudentAndQuiz($user, $quiz);

        if (!$studentQuiz) {
            $this->addFlash('error', 'You have not submitted this quiz yet.');
            return $this->redirectToRoute('app_student_quiz_view', ['id' => $quiz->getId()]);
        }

        return $this->render('student/quiz/results.html.twig', [
            'quiz' => $quiz,
            'studentQuiz' => $studentQuiz,
        ]);
    }
}
