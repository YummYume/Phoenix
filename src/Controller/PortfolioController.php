<?php

namespace App\Controller;

use App\Entity\Portfolio;
use App\Form\PortfolioType;
use App\Repository\PortfolioRepository;
use App\Security\Voter\PortfolioVoter;
use Doctrine\ORM\EntityManagerInterface;
use Knp\Component\Pager\PaginatorInterface;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\Translation\TranslatorInterface;

#[Route('/portfolio')]
final class PortfolioController extends AbstractController
{
    public function __construct(
        private TranslatorInterface $translator,
        private LoggerInterface $logger,
        private EntityManagerInterface $entityManager
    ) {
    }

    #[Route('/', name: 'app_portfolio_index', methods: ['GET'])]
    public function index(Request $request, PaginatorInterface $paginator, PortfolioRepository $portfolioRepository): Response
    {
        $portfolios = $portfolioRepository->findAllByUser($this->getUser());
        $pagination = $paginator->paginate($portfolios, $request->query->getInt('page', 1), 10);

        return $this->render('portfolio/index.html.twig', [
            'pagination' => $pagination,
        ]);
    }

    #[Route('/new', name: 'app_portfolio_new', methods: ['GET', 'POST'])]
    public function new(Request $request): Response
    {
        $portfolio = (new Portfolio())
            ->setResponsible($this->getUser())
        ;
        $form = $this->createForm(PortfolioType::class, $portfolio);
        $form->handleRequest($request);

        if ($form->isSubmitted()) {
            if ($form->isValid()) {
                try {
                    $this->entityManager->persist($portfolio);
                    $this->entityManager->flush();

                    $this->addFlash('success', $this->translator->trans('create.portfolio.success', ['name' => $portfolio->getName()], 'flashes'));

                    return $this->redirectToRoute('app_portfolio_show', ['id' => $portfolio->getId()], Response::HTTP_SEE_OTHER);
                } catch (\Exception $e) {
                    $this->logger->critical($e->getMessage(), ['exception' => $e, 'portfolio' => $portfolio]);
                    $this->addFlash('error', $this->translator->trans('create.portfolio.error', domain: 'flashes'));
                }
            } else {
                $this->addFlash('danger', $this->translator->trans('common.invalid_form', domain: 'flashes'));
            }
        }

        return $this->renderForm('portfolio/new.html.twig', [
            'portfolio' => $portfolio,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_portfolio_show', methods: ['GET'])]
    public function show(Portfolio $portfolio): Response
    {
        $this->denyAccessUnlessGranted(PortfolioVoter::VIEW, $portfolio);

        return $this->render('portfolio/show.html.twig', [
            'portfolio' => $portfolio,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_portfolio_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Portfolio $portfolio): Response
    {
        $this->denyAccessUnlessGranted(PortfolioVoter::EDIT, $portfolio);

        $form = $this->createForm(PortfolioType::class, $portfolio);
        $form->handleRequest($request);

        if ($form->isSubmitted()) {
            if ($form->isValid()) {
                try {
                    $this->entityManager->flush();

                    $this->addFlash('success', $this->translator->trans('edit.portfolio.success', ['name' => $portfolio->getName()], 'flashes'));
                } catch (\Exception $e) {
                    $this->logger->critical($e->getMessage(), ['exception' => $e, 'portfolio' => $portfolio]);
                    $this->addFlash('danger', $this->translator->trans('edit.portfolio.error', domain: 'flashes'));
                }
            } else {
                $this->addFlash('danger', $this->translator->trans('common.invalid_form', domain: 'flashes'));
            }
        }

        return $this->renderForm('portfolio/edit.html.twig', [
            'portfolio' => $portfolio,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_portfolio_delete', methods: ['POST'])]
    public function delete(Request $request, Portfolio $portfolio): Response
    {
        $this->denyAccessUnlessGranted(PortfolioVoter::EDIT, $portfolio);

        if ($this->isCsrfTokenValid('delete'.$portfolio->getId(), $request->request->get('_token'))) {
            try {
                $this->entityManager->remove($portfolio);
                $this->entityManager->flush();
            } catch (\Exception $e) {
                $this->logger->critical($e->getMessage(), ['exception' => $e, 'portfolio' => $portfolio]);
                $this->addFlash('danger', $this->translator->trans('delete.portfolio.error', domain: 'flashes'));
            }
        }

        return $this->redirectToRoute('app_portfolio_index', status: Response::HTTP_SEE_OTHER);
    }
}
