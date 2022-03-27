<?php

namespace App\Controller;

use App\Repository\MilestoneRepository;
use App\Repository\ProjectRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

final class DefaultController extends AbstractController
{
    #[Route('/', name: 'index')]
    public function index(ProjectRepository $projectRepository, MilestoneRepository $milestoneRepository): Response
    {
        $user = $this->getUser();

        if (!$user) {
            return $this->render('default/index.html.twig');
        }

        $activeProjects = $projectRepository->getActiveProjectsByUser($user, false);
        $upcomingProjects = $projectRepository->getUpcomingProjectsByUser($user);
        $projectsWithRisks = $projectRepository->getActiveProjectsByUser($user, true);
        $milestones = $milestoneRepository->getMilestonesByUser($user);

        return $this->render('default/index.html.twig', [
            'activeProjects' => $activeProjects->setMaxResults(10)->getQuery()->getResult(),
            'upcomingProjects' => $upcomingProjects->setMaxResults(10)->getQuery()->getResult(),
            'projectsWithRisks' => $projectsWithRisks->setMaxResults(10)->getQuery()->getResult(),
            'milestones' => $milestones->setMaxResults(10)->getQuery()->getResult(),
        ]);
    }
}
