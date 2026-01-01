<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/system-settings')]
#[IsGranted('ROLE_ADMIN')]
final class SystemSettingsController extends AbstractController
{
    #[Route(name: 'app_system_settings', methods: ['GET'])]
    public function index(Request $request): Response
    {
        $session = $request->getSession();
        
        return $this->render('system_settings/index.html.twig', [
            'allowStudentAccess' => $session->get('allow_student_access', true),
            'allowTeacherAccess' => $session->get('allow_teacher_access', true),
            'maintenanceMode' => $session->get('maintenance_mode', false),
        ]);
    }

    #[Route('/toggle-student-access', name: 'app_system_settings_toggle_student', methods: ['POST'])]
    public function toggleStudentAccess(Request $request): Response
    {
        $session = $request->getSession();
        $currentValue = $session->get('allow_student_access', true);
        $session->set('allow_student_access', !$currentValue);

        $this->addFlash('success', 'Student access ' . (!$currentValue ? 'disabled' : 'enabled'));

        return $this->redirectToRoute('app_system_settings');
    }

    #[Route('/toggle-teacher-access', name: 'app_system_settings_toggle_teacher', methods: ['POST'])]
    public function toggleTeacherAccess(Request $request): Response
    {
        $session = $request->getSession();
        $currentValue = $session->get('allow_teacher_access', true);
        $session->set('allow_teacher_access', !$currentValue);

        $this->addFlash('success', 'Teacher access ' . (!$currentValue ? 'disabled' : 'enabled'));

        return $this->redirectToRoute('app_system_settings');
    }

    #[Route('/toggle-maintenance', name: 'app_system_settings_toggle_maintenance', methods: ['POST'])]
    public function toggleMaintenance(Request $request): Response
    {
        $session = $request->getSession();
        $currentValue = $session->get('maintenance_mode', false);
        $session->set('maintenance_mode', !$currentValue);

        $this->addFlash('success', 'Maintenance mode ' . (!$currentValue ? 'enabled' : 'disabled'));

        return $this->redirectToRoute('app_system_settings');
    }
}
