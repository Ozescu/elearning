<?php

namespace App\Controller;

use App\Entity\StudentGroup;
use App\Form\StudentGroupType;
use App\Repository\StudentGroupRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
#[IsGranted('ROLE_ADMIN')]
#[Route('/student/group')]
final class StudentGroupController extends AbstractController
{
    #[Route(name: 'app_student_group_index', methods: ['GET'])]
    public function index(StudentGroupRepository $studentGroupRepository): Response
    {
        return $this->render('student_group/index.html.twig', [
            'student_groups' => $studentGroupRepository->findAll(),
        ]);
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
