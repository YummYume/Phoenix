<?php

namespace App\Controller;

use App\Entity\Event;
use App\Entity\Milestone;
use App\Entity\Project;
use App\Entity\Risk;
use App\Form\EventType;
use App\Form\MilestoneType;
use App\Form\ProjectType;
use App\Form\RiskType;
use App\Repository\ProjectRepository;
use App\Security\Voter\ProjectVoter;
use Doctrine\ORM\EntityManagerInterface;
use Knp\Component\Pager\PaginatorInterface;
use Psr\Log\LoggerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Entity;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Finder\Finder;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\Translation\TranslatorInterface;

#[Route('/project')]
final class ProjectController extends AbstractController
{
    public function __construct(
        private TranslatorInterface $translator,
        private LoggerInterface $logger,
        private EntityManagerInterface $entityManager
    ) {
    }

    #[Route('/', name: 'app_project_index', methods: ['GET'])]
    public function index(Request $request, PaginatorInterface $paginator, ProjectRepository $projectRepository): Response
    {
        $projects = $projectRepository->getProjectsByUser($this->getUser());
        $pagination = $paginator->paginate($projects, $request->query->getInt('page', 1), 10);

        return $this->render('project/index.html.twig', [
            'pagination' => $pagination,
        ]);
    }

    #[Route('/new', name: 'app_project_new', methods: ['GET', 'POST'])]
    public function new(Request $request): Response
    {
        $project = new Project();
        $form = $this->createForm(ProjectType::class, $project);
        $form->handleRequest($request);

        if ($form->isSubmitted()) {
            if ($form->isValid()) {
                try {
                    $this->entityManager->persist($project);
                    $this->entityManager->flush();

                    $this->addFlash('success', $this->translator->trans('create.project.success', ['name' => $project->getName()], 'flashes'));

                    return $this->redirectToRoute('app_project_show', ['code' => $project->getCode()], Response::HTTP_SEE_OTHER);
                } catch (\Exception $e) {
                    $this->logger->critical($e->getMessage(), ['exception' => $e, 'project' => $project]);
                    $this->addFlash('error', $this->translator->trans('create.project.error', domain: 'flashes'));
                }
            } else {
                $this->addFlash('danger', $this->translator->trans('common.invalid_form', domain: 'flashes'));
            }
        }

        return $this->renderForm('project/new.html.twig', [
            'project' => $project,
            'form' => $form,
        ]);
    }

    #[Route('/{code}', name: 'app_project_show', methods: ['GET'])]
    public function show(Project $project): Response
    {
        $this->denyAccessUnlessGranted(ProjectVoter::VIEW, $project);

        $finder = new Finder();
        $ai = null;

        $finder
            ->in(__DIR__.'\\..\\..\\data')
            ->name('projectAi.min.json')
        ;

        foreach ($finder as $file) {
            $ai = $file->getContents();
        }

        return $this->render('project/show.html.twig', [
            'project' => $project,
            'ai' => $ai,
        ]);
    }

    #[Route('/{code}/edit', name: 'app_project_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Project $project): Response
    {
        $this->denyAccessUnlessGranted(ProjectVoter::EDIT, $project);

        $form = $this->createForm(ProjectType::class, $project);
        $form->handleRequest($request);

        if ($form->isSubmitted()) {
            if ($form->isValid()) {
                try {
                    $this->entityManager->flush();

                    $this->addFlash('success', $this->translator->trans('edit.project.success', ['name' => $project->getName()], 'flashes'));

                    // Trick to redirect to the newly generated slug if the name of the project changed
                    return $this->redirectToRoute('app_project_edit', ['code' => $project->getCode()], Response::HTTP_SEE_OTHER);
                } catch (\Exception $e) {
                    $this->logger->critical($e->getMessage(), ['exception' => $e, 'project' => $project]);
                    $this->addFlash('danger', $this->translator->trans('edit.project.error', domain: 'flashes'));
                }
            } else {
                $this->addFlash('danger', $this->translator->trans('common.invalid_form', domain: 'flashes'));
            }
        }

        return $this->renderForm('project/edit.html.twig', [
            'project' => $project,
            'form' => $form,
        ]);
    }

    #[Route('/{code}/delete', name: 'app_project_delete', methods: ['POST'])]
    public function delete(Request $request, Project $project): Response
    {
        $this->denyAccessUnlessGranted(ProjectVoter::EDIT, $project);

        if ($this->isCsrfTokenValid('delete'.$project->getId(), $request->request->get('_token'))) {
            try {
                $this->entityManager->remove($project);
                $this->entityManager->flush();

                $this->addFlash('success', $this->translator->trans('delete.project.success', ['name' => $project->getName()], 'flashes'));
            } catch (\Exception $e) {
                $this->logger->critical($e->getMessage(), ['exception' => $e, 'project' => $project]);
                $this->addFlash('danger', $this->translator->trans('delete.project.error', domain: 'flashes'));
            }
        }

        return $this->redirectToRoute('app_project_index', status: Response::HTTP_SEE_OTHER);
    }

    #[Route('/{code}/milestone/new', name: 'app_project_milestone_new', methods: ['GET', 'POST'])]
    public function newMilestone(Request $request, Project $project): Response
    {
        $this->denyAccessUnlessGranted(ProjectVoter::EDIT_MILESTONE, $project);

        $milestone = (new Milestone())
            ->setProject($project)
        ;
        $form = $this->createForm(MilestoneType::class, $milestone);
        $form->handleRequest($request);

        if ($form->isSubmitted()) {
            if ($form->isValid()) {
                try {
                    $this->entityManager->persist($milestone);
                    $this->entityManager->flush();

                    $this->addFlash('success', $this->translator->trans('create.milestone.success', ['name' => $milestone->getName()], 'flashes'));

                    return $this->redirectToRoute('app_project_milestone_edit', [
                        'code' => $project->getCode(),
                        'milestone_id' => $milestone->getId(),
                    ], Response::HTTP_SEE_OTHER);
                } catch (\Exception $e) {
                    $this->logger->critical($e->getMessage(), ['exception' => $e, 'milestone' => $milestone]);
                    $this->addFlash('error', $this->translator->trans('create.milestone.error', domain: 'flashes'));
                }
            } else {
                $this->addFlash('danger', $this->translator->trans('common.invalid_form', domain: 'flashes'));
            }
        }

        return $this->renderForm('project/milestone/new.html.twig', [
            'milestone' => $milestone,
            'form' => $form,
        ]);
    }

    #[Route('/{code}/milestone/{milestone_id}/edit', name: 'app_project_milestone_edit', methods: ['GET', 'POST'])]
    #[Entity('milestone', expr: 'repository.find(milestone_id)')]
    public function editMilestone(Request $request, Project $project, Milestone $milestone): Response
    {
        if ($project !== $milestone->getProject()) {
            throw new NotFoundHttpException('Milestone not found.');
        }

        $this->denyAccessUnlessGranted(ProjectVoter::EDIT_MILESTONE, $project);

        $form = $this->createForm(MilestoneType::class, $milestone);
        $form->handleRequest($request);

        if ($form->isSubmitted()) {
            if ($form->isValid()) {
                try {
                    $this->entityManager->flush();

                    $this->addFlash('success', $this->translator->trans('edit.milestone.success', ['name' => $milestone->getName()], 'flashes'));
                } catch (\Exception $e) {
                    $this->logger->critical($e->getMessage(), ['exception' => $e, 'milestone' => $milestone]);
                    $this->addFlash('danger', $this->translator->trans('edit.milestone.error', domain: 'flashes'));
                }
            } else {
                $this->addFlash('danger', $this->translator->trans('common.invalid_form', domain: 'flashes'));
            }
        }

        return $this->renderForm('project/milestone/edit.html.twig', [
            'milestone' => $milestone,
            'form' => $form,
        ]);
    }

    #[Route('/{code}/milestone/{milestone_id}/delete', name: 'app_project_milestone_delete', methods: ['POST'])]
    #[Entity('milestone', expr: 'repository.find(milestone_id)')]
    public function deleteMilestone(Request $request, Project $project, Milestone $milestone): Response
    {
        if ($project !== $milestone->getProject()) {
            throw new NotFoundHttpException('Milestone not found.');
        }

        $this->denyAccessUnlessGranted(ProjectVoter::EDIT_MILESTONE, $project);

        if ($this->isCsrfTokenValid('delete'.$milestone->getId(), $request->request->get('_token'))) {
            try {
                $this->entityManager->remove($milestone);
                $this->entityManager->flush();

                $this->addFlash('success', $this->translator->trans('delete.milestone.success', ['name' => $milestone->getName()], 'flashes'));
            } catch (\Exception $e) {
                $this->logger->critical($e->getMessage(), ['exception' => $e, 'milestone' => $milestone]);
                $this->addFlash('danger', $this->translator->trans('delete.milestone.error', domain: 'flashes'));
            }
        }

        return $this->redirectToRoute('app_project_show', ['code' => $project->getCode()], Response::HTTP_SEE_OTHER);
    }

    #[Route('/{code}/milestone/{milestone_id}/move', name: 'app_project_milestone_move', condition: 'request.isXmlHttpRequest()', methods: ['POST'])]
    #[Entity('milestone', expr: 'repository.find(milestone_id)')]
    public function moveMilestone(Request $request, Project $project, Milestone $milestone): JsonResponse
    {
        $response = [];

        try {
            if ($project !== $milestone->getProject()) {
                $response['status'] = 'error';
                $response['message'] = $this->translator->trans('milestone.move.error', domain: 'flashes');
            } elseif (!$this->isGranted(ProjectVoter::EDIT_MILESTONE, $project)) {
                $response['status'] = 'error';
                $response['message'] = $this->translator->trans('milestone.move.not_allowed', domain: 'flashes');
            } else {
                $oldPosition = $request->request->get('oldPosition');
                $newPosition = $request->request->get('newPosition');

                if (null === $oldPosition || null === $newPosition || $oldPosition === $newPosition || intval($oldPosition) !== $milestone->getPosition()) {
                    $response['status'] = 'error';
                    $response['message'] = $this->translator->trans('milestone.move.error', domain: 'flashes');
                } else {
                    $milestone->setPosition(intval($newPosition));

                    $this->entityManager->flush();

                    $response['status'] = 'success';
                    $response['message'] = $this->translator->trans('milestone.move.success', ['name' => $milestone->getName()], 'flashes');
                }
            }
        } catch (\Exception $e) {
            $this->logger->critical($e->getMessage(), ['exception' => $e, 'milestone' => $milestone]);
            $response['status'] = 'error';
            $response['message'] = $this->translator->trans('milestone.move.error', domain: 'flashes');
        }

        return new JsonResponse($response);
    }

    #[Route('/{code}/risk/new', name: 'app_project_risk_new', methods: ['GET', 'POST'])]
    public function newRisk(Request $request, Project $project): Response
    {
        $this->denyAccessUnlessGranted(ProjectVoter::EDIT_RISK, $project);

        $risk = (new Risk())
            ->setProject($project)
        ;
        $form = $this->createForm(RiskType::class, $risk);
        $form->handleRequest($request);

        if ($form->isSubmitted()) {
            if ($form->isValid()) {
                try {
                    $this->entityManager->persist($risk);
                    $this->entityManager->flush();

                    $this->addFlash('success', $this->translator->trans('create.risk.success', ['name' => $risk->getName()], 'flashes'));

                    return $this->redirectToRoute('app_project_risk_edit', [
                        'code' => $project->getCode(),
                        'risk_id' => $risk->getId(),
                    ], Response::HTTP_SEE_OTHER);
                } catch (\Exception $e) {
                    $this->logger->critical($e->getMessage(), ['exception' => $e, 'risk' => $risk]);
                    $this->addFlash('error', $this->translator->trans('create.risk.error', domain: 'flashes'));
                }
            } else {
                $this->addFlash('danger', $this->translator->trans('common.invalid_form', domain: 'flashes'));
            }
        }

        return $this->renderForm('project/risk/new.html.twig', [
            'risk' => $risk,
            'form' => $form,
        ]);
    }

    #[Route('/{code}/risk/{risk_id}/edit', name: 'app_project_risk_edit', methods: ['GET', 'POST'])]
    #[Entity('risk', expr: 'repository.find(risk_id)')]
    public function editRisk(Request $request, Project $project, Risk $risk): Response
    {
        if ($project !== $risk->getProject()) {
            throw new NotFoundHttpException('Risk not found.');
        }

        $this->denyAccessUnlessGranted(ProjectVoter::EDIT_RISK, $project);

        $form = $this->createForm(RiskType::class, $risk);
        $form->handleRequest($request);

        if ($form->isSubmitted()) {
            if ($form->isValid()) {
                try {
                    $this->entityManager->flush();

                    $this->addFlash('success', $this->translator->trans('edit.risk.success', ['name' => $risk->getName()], 'flashes'));
                } catch (\Exception $e) {
                    $this->logger->critical($e->getMessage(), ['exception' => $e, 'risk' => $risk]);
                    $this->addFlash('danger', $this->translator->trans('edit.risk.error', domain: 'flashes'));
                }
            } else {
                $this->addFlash('danger', $this->translator->trans('common.invalid_form', domain: 'flashes'));
            }
        }

        return $this->renderForm('project/risk/edit.html.twig', [
            'risk' => $risk,
            'form' => $form,
        ]);
    }

    #[Route('/{code}/risk/{risk_id}/delete', name: 'app_project_risk_delete', methods: ['POST'])]
    #[Entity('risk', expr: 'repository.find(risk_id)')]
    public function deleteRisk(Request $request, Project $project, Risk $risk): Response
    {
        if ($project !== $risk->getProject()) {
            throw new NotFoundHttpException('Risk not found.');
        }

        $this->denyAccessUnlessGranted(ProjectVoter::EDIT_RISK, $project);

        if ($this->isCsrfTokenValid('delete'.$risk->getId(), $request->request->get('_token'))) {
            try {
                $this->entityManager->remove($risk);
                $this->entityManager->flush();

                $this->addFlash('success', $this->translator->trans('delete.risk.success', ['name' => $risk->getName()], 'flashes'));
            } catch (\Exception $e) {
                $this->logger->critical($e->getMessage(), ['exception' => $e, 'risk' => $risk]);
                $this->addFlash('danger', $this->translator->trans('delete.risk.error', domain: 'flashes'));
            }
        }

        return $this->redirectToRoute('app_project_show', ['code' => $project->getCode()], Response::HTTP_SEE_OTHER);
    }

    #[Route('/{code}/event/new', name: 'app_project_event_new', methods: ['GET', 'POST'])]
    public function newEvent(Request $request, Project $project): Response
    {
        $this->denyAccessUnlessGranted(ProjectVoter::EDIT_EVENT, $project);

        $event = (new Event())
            ->setProject($project)
        ;
        $form = $this->createForm(EventType::class, $event);
        $form->handleRequest($request);

        if ($form->isSubmitted()) {
            if ($form->isValid()) {
                try {
                    $this->entityManager->persist($event);
                    $this->entityManager->flush();

                    $this->addFlash('success', $this->translator->trans('create.event.success', ['name' => $event->getName()], 'flashes'));

                    return $this->redirectToRoute('app_project_event_edit', [
                        'code' => $project->getCode(),
                        'event_id' => $event->getId(),
                    ], Response::HTTP_SEE_OTHER);
                } catch (\Exception $e) {
                    $this->logger->critical($e->getMessage(), ['exception' => $e, 'event' => $event]);
                    $this->addFlash('error', $this->translator->trans('create.event.error', domain: 'flashes'));
                }
            } else {
                $this->addFlash('danger', $this->translator->trans('common.invalid_form', domain: 'flashes'));
            }
        }

        return $this->renderForm('project/event/new.html.twig', [
            'event' => $event,
            'form' => $form,
        ]);
    }

    #[Route('/{code}/event/{event_id}/edit', name: 'app_project_event_edit', methods: ['GET', 'POST'])]
    #[Entity('event', expr: 'repository.find(event_id)')]
    public function editEvent(Request $request, Project $project, Event $event): Response
    {
        if ($project !== $event->getProject()) {
            throw new NotFoundHttpException('Event not found.');
        }

        $this->denyAccessUnlessGranted(ProjectVoter::EDIT_EVENT, $project);

        $form = $this->createForm(EventType::class, $event);
        $form->handleRequest($request);

        if ($form->isSubmitted()) {
            if ($form->isValid()) {
                try {
                    $this->entityManager->flush();

                    $this->addFlash('success', $this->translator->trans('edit.event.success', ['name' => $event->getName()], 'flashes'));
                } catch (\Exception $e) {
                    $this->logger->critical($e->getMessage(), ['exception' => $e, 'event' => $event]);
                    $this->addFlash('danger', $this->translator->trans('edit.event.error', domain: 'flashes'));
                }
            } else {
                $this->addFlash('danger', $this->translator->trans('common.invalid_form', domain: 'flashes'));
            }
        }

        return $this->renderForm('project/event/edit.html.twig', [
            'event' => $event,
            'form' => $form,
        ]);
    }

    #[Route('/{code}/event/{event_id}/delete', name: 'app_project_event_delete', methods: ['POST'])]
    #[Entity('event', expr: 'repository.find(event_id)')]
    public function deleteEvent(Request $request, Project $project, Event $event): Response
    {
        if ($project !== $event->getProject()) {
            throw new NotFoundHttpException('Event not found.');
        }

        $this->denyAccessUnlessGranted(ProjectVoter::EDIT_EVENT, $project);

        if ($this->isCsrfTokenValid('delete'.$event->getId(), $request->request->get('_token'))) {
            try {
                $this->entityManager->remove($event);
                $this->entityManager->flush();

                $this->addFlash('success', $this->translator->trans('delete.event.success', ['name' => $event->getName()], 'flashes'));
            } catch (\Exception $e) {
                $this->logger->critical($e->getMessage(), ['exception' => $e, 'event' => $event]);
                $this->addFlash('danger', $this->translator->trans('delete.event.error', domain: 'flashes'));
            }
        }

        return $this->redirectToRoute('app_project_show', ['code' => $project->getCode()], Response::HTTP_SEE_OTHER);
    }
}
