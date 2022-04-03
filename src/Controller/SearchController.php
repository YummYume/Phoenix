<?php

namespace App\Controller;

use App\Enum\Role;
use App\Repository\ProjectRepository;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\Translation\TranslatorInterface;

final class SearchController extends AbstractController
{
    #[Route('/search', name: 'app_search')]
    public function search(
        Request $request,
        PaginatorInterface $paginator,
        ProjectRepository $projectRepository,
        TranslatorInterface $translator
    ): Response {
        $query = trim($request->get('q'));

        if (empty($query)) {
            $this->addFlash('warning', $translator->trans('common.empty_search', domain: 'flashes'));
        }

        $projects = $projectRepository->getAllProjects($query, $this->isGranted(Role::Admin) ? null : $this->getUser())->getQuery()->getResult();
        $pagination = $paginator->paginate($projects, $request->query->getInt('page', 1), 5);

        return $this->render('search/index.html.twig', [
            'pagination' => $pagination,
        ]);
    }
}
