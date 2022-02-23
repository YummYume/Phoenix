<?php

namespace App\Controller;

use App\Entity\Milestone;
use App\Form\MilestoneType;
use App\Repository\MilestoneRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/milestone')]
class MilestoneController extends AbstractController
{
    #[Route('/', name: 'milestone_index', methods: ['GET'])]
    public function index(MilestoneRepository $milestoneRepository): Response
    {
        return $this->render('milestone/index.html.twig', [
            'milestones' => $milestoneRepository->findAll(),
        ]);
    }

    #[Route('/new', name: 'milestone_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $milestone = new Milestone();
        $form = $this->createForm(MilestoneType::class, $milestone);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($milestone);
            $entityManager->flush();

            return $this->redirectToRoute('milestone_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('milestone/new.html.twig', [
            'milestone' => $milestone,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'milestone_show', methods: ['GET'])]
    public function show(Milestone $milestone): Response
    {
        return $this->render('milestone/show.html.twig', [
            'milestone' => $milestone,
        ]);
    }

    #[Route('/{id}/edit', name: 'milestone_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Milestone $milestone, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(MilestoneType::class, $milestone);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('milestone_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('milestone/edit.html.twig', [
            'milestone' => $milestone,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'milestone_delete', methods: ['POST'])]
    public function delete(Request $request, Milestone $milestone, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$milestone->getId(), $request->request->get('_token'))) {
            $entityManager->remove($milestone);
            $entityManager->flush();
        }

        return $this->redirectToRoute('milestone_index', [], Response::HTTP_SEE_OTHER);
    }
}
