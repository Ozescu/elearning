<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\ExpressionLanguage\Expression;
use Symfony\Component\Security\Http\Attribute\IsGranted;

final class TeacherController extends AbstractController
{
    #[Route('/teacher', name: 'app_teacher')]
    #[IsGranted(new Expression("is_granted('ROLE_TEACHER') or is_granted('ROLE_ADMIN')"))]
    public function index(): Response
    {
        return $this->render('teacher/index.html.twig');
    }
}
