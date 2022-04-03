<?php

namespace App\Controller;

use App\Entity\User;
use App\Enum\Role;
use App\Form\UserType;
use App\Security\LoginFormAuthenticator;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Authentication\UserAuthenticatorInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

final class UserController extends AbstractController
{
    public function __construct(
        private TranslatorInterface $translator,
        private LoggerInterface $logger,
        private EntityManagerInterface $entityManager
    ) {
    }

    #[Route('/register', name: 'app_register')]
    public function register(
        Request $request,
        UserAuthenticatorInterface $userAuthenticator,
        LoginFormAuthenticator $authenticator
    ): Response {
        if ($this->getUser()) {
            return $this->redirectToRoute('app_index');
        }

        $user = new User();
        $form = $this->createForm(UserType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted()) {
            if ($form->isValid()) {
                try {
                    $user->setRoles([Role::User->value]);
                    $this->entityManager->persist($user);
                    $this->entityManager->flush();

                    $this->addFlash('success', $this->translator->trans('create.user.success', ['name' => $user->getFullName() ?? $user->getEmail()], 'flashes'));

                    return $userAuthenticator->authenticateUser(
                        $user,
                        $authenticator,
                        $request
                    );
                } catch (\Exception $e) {
                    $this->logger->critical($e->getMessage(), ['exception' => $e, 'user' => $user]);
                    $this->addFlash('danger', $this->translator->trans('create.user.error', domain: 'flashes'));
                }
            } else {
                $this->addFlash('danger', $this->translator->trans('common.invalid_form', domain: 'flashes'));
            }
        }

        return $this->render('user/register.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    #[Route('/profile/edit', name: 'app_edit_profile')]
    public function edit(Request $request): Response
    {
        $user = $this->getUser();
        $form = $this->createForm(UserType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted()) {
            if ($form->isValid()) {
                try {
                    $this->entityManager->flush();

                    $this->addFlash('success', $this->translator->trans('edit.user.success', domain: 'flashes'));
                } catch (\Exception $e) {
                    $this->logger->critical($e->getMessage(), ['exception' => $e, 'user' => $user]);
                    $this->addFlash('danger', $this->translator->trans('edit.user.error', domain: 'flashes'));
                }
            } else {
                $this->addFlash('danger', $this->translator->trans('common.invalid_form', domain: 'flashes'));
            }
        }

        return $this->render('user/edit.html.twig', [
            'form' => $form->createView(),
        ]);
    }
}
