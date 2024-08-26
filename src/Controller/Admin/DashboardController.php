<?php

namespace App\Controller\Admin;

use App\Entity\Category;
use App\Entity\Group;
use App\Entity\ImportRun;
use App\Entity\Report;
use App\Entity\System;
use App\Entity\Theme;
use App\Entity\User;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Config\Dashboard;
use EasyCorp\Bundle\EasyAdminBundle\Config\MenuItem;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractDashboardController;
use EasyCorp\Bundle\EasyAdminBundle\Router\AdminUrlGenerator;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class DashboardController extends AbstractDashboardController
{
    /**
     * @Route("/admin")
     */

     public function index(): Response
    {

         $adminUrlGenerator = $this->container->get(AdminUrlGenerator::class);
         return $this->redirect($adminUrlGenerator->setController(AdminContent::class)->generateUrl());
    }
    public function configureDashboard(): Dashboard
    {
        return Dashboard::new()
            ->setTitle('Sysstatus')
            ->setFaviconPath('favicon.ico');

    }
    public function configureCrud(): Crud
    {
        return Crud::new()
            ->overrideTemplate('label/null', 'easy_admin_overrides/label_null.html.twig');
    }

    public function configureMenuItems(): iterable
    {
        return [

            MenuItem::section('Dashboard '),
                  MenuItem::linkToCrud('menu.dashboard.reports', 'fas fa-file-alt', Report::class)->setController(DashboardReportCrudController::class),
                  MenuItem::linkToCrud('menu.dashboard.systems', 'fas fa-cogs', System::class)->setController(DashboardSystemCrudController::class),

            MenuItem::section('Sysstatus'),
                MenuItem::linkToCrud('menu.list.reports', 'fas fa-list', Report::class),
                MenuItem::linkToCrud('menu.list.system', 'fas fa-cog', System::class),

            MenuItem::section('Konfiguration'),
                MenuItem::linkToCrud('menu.themes', 'fas fa-th-large', Theme::class),
                MenuItem::linkToCrud('menu.categories', 'fas fa-list', Category::class),

            MenuItem::section('Administration'),
                MenuItem::linkToCrud('User', 'fas fa-user', User::class),
                MenuItem::linkToCrud('Group', 'fas fa-users', Group::class),
                MenuItem::linkToCrud('Import kørsler ', 'fas fa-file-excel', ImportRun::class),
                MenuItem::linkToRoute('Eksport', 'fas fa-file-import', 'export_page'),
        ];

    }
}
