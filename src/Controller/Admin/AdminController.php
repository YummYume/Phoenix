<?php

namespace App\Controller\Admin;

use App\Entity\Status;
use App\Entity\Team;
use App\Entity\User;
use App\Enum\Role;
use EasyCorp\Bundle\EasyAdminBundle\Config\Dashboard;
use EasyCorp\Bundle\EasyAdminBundle\Config\MenuItem;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractDashboardController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class AdminController extends AbstractDashboardController
{
    #[Route('/admin', name: 'admin')]
    public function index(): Response
    {
        if (!$this->isGranted(Role::Admin->value) && !$this->isGranted(Role::SuperAdmin->value)) {
            $this->redirectToRoute('app_login');
        }

        return $this->render('admin/index.html.twig');
    }

    public function configureDashboard(): Dashboard
    {
        return Dashboard::new()
            ->setTitle('Administration')
        ;
    }

    public function configureMenuItems(): iterable
    {
        yield MenuItem::linkToDashboard('menu.dashboard', 'fa fa-home');
        yield MenuItem::linkToCrud('menu.status', 'fas fa-list', Status::class);
        yield MenuItem::linkToCrud('menu.team', 'fas fa-list', Team::class);
        yield MenuItem::linkToCrud('menu.user', 'fas fa-list', User::class);
    }
}
