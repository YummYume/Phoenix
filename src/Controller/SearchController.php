<?php

namespace App\Controller;

use App\Enum\Role;
use App\Repository\ProjectRepository;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class SearchController extends AbstractController
{
    #[Route('/search', name: 'app_search')]
    public function search(Request $request, PaginatorInterface $paginator, ProjectRepository $projectRepository): Response
    {
        $query = trim($request->get('q'));

        if (empty($query)) {
            $this->addFlash('warning', '<i class="fa fa-exclamation-circle"></i> Votre recherche est vide.');
        }

        $projects = $projectRepository->getAllProjects($query, $this->isGranted(Role::Admin) ? null : $this->getUser())->getQuery()->getResult();
        $pagination = $paginator->paginate($projects, $request->query->getInt('page', 1), 5);

        return $this->render('search/index.html.twig', [
            'pagination' => $pagination,
        ]);
    }
}
