<?php

namespace App\Controller;

use App\Repository\UserRepository;
use App\Repository\GroupRepository;
use App\Repository\StudentGroupRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/reports')]
#[IsGranted('ROLE_ADMIN')]
final class ReportsController extends AbstractController
{
    #[Route(name: 'app_reports')]
    public function index(
        UserRepository $userRepository,
        GroupRepository $groupRepository,
        StudentGroupRepository $studentGroupRepository
    ): Response {
        $totalUsers = count($userRepository->findAll());
        $totalAdmins = count($userRepository->findBy(['roles' => 'ROLE_ADMIN']));
        $totalTeachers = count($userRepository->findBy(['roles' => 'ROLE_TEACHER']));
        $totalStudents = count($userRepository->findBy(['roles' => 'ROLE_STUDENT']));
        $totalGroups = count($groupRepository->findAll());
        $totalAssignments = count($studentGroupRepository->findAll());
        $approvedAssignments = count($studentGroupRepository->findBy(['approved' => true]));
        $pendingAssignments = $totalAssignments - $approvedAssignments;

        return $this->render('reports/index.html.twig', [
            'totalUsers' => $totalUsers,
            'totalAdmins' => $totalAdmins,
            'totalTeachers' => $totalTeachers,
            'totalStudents' => $totalStudents,
            'totalGroups' => $totalGroups,
            'totalAssignments' => $totalAssignments,
            'approvedAssignments' => $approvedAssignments,
            'pendingAssignments' => $pendingAssignments,
        ]);
    }
}
