<?php

namespace App\Controller;

use App\Entity\StudentGroup;
use App\Form\StudentGroupType;
use App\Repository\StudentGroupRepository;
use App\Repository\UserRepository;
use App\Repository\GroupRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\ExpressionLanguage\Expression;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/student/group')]
final class StudentGroupController extends AbstractController
{
    #[IsGranted(new Expression("is_granted('ROLE_TEACHER') or is_granted('ROLE_ADMIN')"))]
    #[Route(name: 'app_student_group_index', methods: ['GET'])]
    public function index(StudentGroupRepository $studentGroupRepository): Response
    {
        return $this->render('student_group/index.html.twig', [
            'student_groups' => $studentGroupRepository->findAll(),
        ]);
    }

    #[IsGranted('ROLE_ADMIN')]
    #[Route('/admin', name: 'app_admin_student_group_index', methods: ['GET'])]
    public function adminIndex(StudentGroupRepository $studentGroupRepository, UserRepository $userRepository, GroupRepository $groupRepository): Response
    {
        return $this->render('student_group/admin_index.html.twig', [
            'student_groups' => $studentGroupRepository->findAll(),
            'students' => $userRepository->findByRole('ROLE_STUDENT'),
            'groups' => $groupRepository->findAll(),
        ]);
    }

    #[IsGranted('ROLE_ADMIN')]
    #[Route('/admin/create', name: 'app_admin_student_group_create', methods: ['POST'])]
    public function adminCreate(Request $request, UserRepository $userRepository, GroupRepository $groupRepository, EntityManagerInterface $entityManager): Response
    {
        $studentId = $request->getPayload()->get('student_id');
        $groupId = $request->getPayload()->get('group_id');

        $student = $userRepository->find($studentId);
        $group = $groupRepository->find($groupId);

        if (!$student || !$group) {
            $this->addFlash('error', 'Invalid student or group');
            return $this->redirectToRoute('app_admin_student_group_index');
        }

        // Check if assignment already exists
        $existing = $entityManager->getRepository(StudentGroup::class)->findOneBy([
            'student' => $student,
            'group' => $group,
        ]);

        if ($existing) {
            $this->addFlash('warning', 'This student is already assigned to this group');
            return $this->redirectToRoute('app_admin_student_group_index');
        }

        $studentGroup = new StudentGroup();
        $studentGroup->setStudent($student);
        $studentGroup->setGroup($group);
        $studentGroup->setApproved(true);

        $entityManager->persist($studentGroup);
        $entityManager->flush();

        $this->addFlash('success', 'Student assigned to group successfully');
        return $this->redirectToRoute('app_admin_student_group_index');
    }

    #[IsGranted('ROLE_ADMIN')]
    #[Route('/admin/{id}/delete', name: 'app_admin_student_group_delete', methods: ['POST'])]
    public function adminDelete(Request $request, StudentGroup $studentGroup, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$studentGroup->getId(), $request->getPayload()->getString('_token'))) {
            $entityManager->remove($studentGroup);
            $entityManager->flush();
            $this->addFlash('success', 'Student removed from group');
        }

        return $this->redirectToRoute('app_admin_student_group_index');
    }

    #[Route('/new', name: 'app_student_group_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {

        $studentGroup = new StudentGroup();
        $form = $this->createForm(StudentGroupType::class, $studentGroup);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($studentGroup);
            $entityManager->flush();

            return $this->redirectToRoute('app_student_group_index', [], Response::HTTP_SEE_OTHER);
        }
        $studentGroup->setApproved(true);
        return $this->render('student_group/new.html.twig', [
            'student_group' => $studentGroup,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_student_group_show', methods: ['GET'])]
    public function show(StudentGroup $studentGroup): Response
    {
        return $this->render('student_group/show.html.twig', [
            'student_group' => $studentGroup,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_student_group_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, StudentGroup $studentGroup, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(StudentGroupType::class, $studentGroup);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_student_group_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('student_group/edit.html.twig', [
            'student_group' => $studentGroup,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_student_group_delete', methods: ['POST'])]
    public function delete(Request $request, StudentGroup $studentGroup, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$studentGroup->getId(), $request->getPayload()->getString('_token'))) {
            $entityManager->remove($studentGroup);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_student_group_index', [], Response::HTTP_SEE_OTHER);
    }
}
