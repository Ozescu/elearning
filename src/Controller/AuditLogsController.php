<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/audit-logs')]
#[IsGranted('ROLE_ADMIN')]
final class AuditLogsController extends AbstractController
{
    #[Route(name: 'app_audit_logs')]
    public function index(): Response
    {
        // Placeholder for audit logs - would be populated from a database log table
        $logs = [
            ['timestamp' => '2026-01-01 09:15', 'user' => 'admin@emsi.ma', 'action' => 'Created user', 'resource' => 'teacher@emsi.ma', 'status' => 'Success'],
            ['timestamp' => '2026-01-01 08:45', 'user' => 'admin@emsi.ma', 'action' => 'Updated group', 'resource' => '1A', 'status' => 'Success'],
            ['timestamp' => '2026-01-01 08:30', 'user' => 'admin@emsi.ma', 'action' => 'Deleted user', 'resource' => 'olduser@emsi.ma', 'status' => 'Success'],
            ['timestamp' => '2026-01-01 08:15', 'user' => 'teacher@emsi.ma', 'action' => 'Login', 'resource' => 'Login attempt', 'status' => 'Success'],
        ];

        return $this->render('audit_logs/index.html.twig', [
            'logs' => $logs,
        ]);
    }
}
