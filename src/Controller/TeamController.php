<?php

namespace App\Controller;

use App\Entity\Team;
use App\Form\TeamType;
use App\Repository\TeamRepository;
use App\Security\Voter\TeamVoter;
use Doctrine\ORM\EntityManagerInterface;
use Knp\Component\Pager\PaginatorInterface;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\Translation\TranslatorInterface;

#[Route('/team')]
final class TeamController extends AbstractController
{
    public function __construct(
        private TranslatorInterface $translator,
        private LoggerInterface $logger,
        private EntityManagerInterface $entityManager
    ) {
    }

    #[Route('/', name: 'app_team_index', methods: ['GET'])]
    public function index(Request $request, PaginatorInterface $paginator, TeamRepository $teamRepository): Response
    {
        $teams = $teamRepository->findAllByUser($this->getUser());
        $pagination = $paginator->paginate($teams, $request->query->getInt('page', 1), 10);

        return $this->render('team/index.html.twig', [
            'pagination' => $pagination,
        ]);
    }

    #[Route('/new', name: 'app_team_new', methods: ['GET', 'POST'])]
    public function new(Request $request): Response
    {
        $team = (new Team())
            ->setResponsible($this->getUser())
            ->addMember($this->getUser())
        ;

        $form = $this->createForm(TeamType::class, $team);
        $form->handleRequest($request);

        if ($form->isSubmitted()) {
            if ($form->isValid()) {
                try {
                    $this->entityManager->persist($team);
                    $this->entityManager->flush();

                    $this->addFlash('success', $this->translator->trans('create.team.success', ['name' => $team->getName()], 'flashes'));

                    return $this->redirectToRoute('app_team_show', ['id' => $team->getId()], Response::HTTP_SEE_OTHER);
                } catch (\Exception $e) {
                    $this->logger->critical($e->getMessage(), ['exception' => $e, 'team' => $team]);
                    $this->addFlash('error', $this->translator->trans('create.team.error', domain: 'flashes'));
                }
            } else {
                $this->addFlash('danger', $this->translator->trans('common.invalid_form', domain: 'flashes'));
            }
        }

        return $this->renderForm('team/new.html.twig', [
            'team' => $team,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_team_show', methods: ['GET'])]
    public function show(Team $team): Response
    {
        $this->denyAccessUnlessGranted(TeamVoter::VIEW, $team);

        return $this->render('team/show.html.twig', [
            'team' => $team,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_team_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Team $team): Response
    {
        $this->denyAccessUnlessGranted(TeamVoter::EDIT, $team);

        $form = $this->createForm(TeamType::class, $team);
        $form->handleRequest($request);

        if ($form->isSubmitted()) {
            if ($form->isValid()) {
                try {
                    $this->entityManager->flush();

                    $this->addFlash('success', $this->translator->trans('edit.team.success', ['name' => $team->getName()], 'flashes'));
                } catch (\Exception $e) {
                    $this->logger->critical($e->getMessage(), ['exception' => $e, 'team' => $team]);
                    $this->addFlash('danger', $this->translator->trans('edit.team.error', domain: 'flashes'));
                }
            } else {
                $this->addFlash('danger', $this->translator->trans('common.invalid_form', domain: 'flashes'));
            }
        }

        return $this->renderForm('team/edit.html.twig', [
            'team' => $team,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_team_delete', methods: ['POST'])]
    public function delete(Request $request, Team $team, EntityManagerInterface $entityManager): Response
    {
        $this->denyAccessUnlessGranted(TeamVoter::EDIT, $team);

        if ($this->isCsrfTokenValid('delete'.$team->getId(), $request->request->get('_token'))) {
            try {
                $entityManager->remove($team);
                $entityManager->flush();

                $this->addFlash('success', $this->translator->trans('delete.team.success', ['name' => $team->getName()], 'flashes'));
            } catch (\Exception $e) {
                $this->logger->critical($e->getMessage(), ['exception' => $e, 'team' => $team]);
                $this->addFlash('danger', $this->translator->trans('delete.team.error', domain: 'flashes'));
            }
        }

        return $this->redirectToRoute('app_team_index', status: Response::HTTP_SEE_OTHER);
    }
}
