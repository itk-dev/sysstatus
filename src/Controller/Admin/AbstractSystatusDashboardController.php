<?php

namespace App\Controller\Admin;

use App\Entity\Answer;
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
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;
use Symfony\Component\HttpFoundation\Response;

abstract class AbstractSystatusDashboardController extends AbstractDashboardController
{
    public function configureDashboard(): Dashboard
    {
        return Dashboard::new()
            ->setTitle('Sysstatus')
            ->setFaviconPath('favicon.ico')
        ;
    }


    public function configureCrud(): Crud
    {
        return Crud::new()
            ->overrideTemplate('label/null', 'easy_admin_overrides/label_null.html.twig')
        ;
    }

    public function configureMenuItems(): iterable
    {
        return [
            MenuItem::section('Dashboard '),
            MenuItem::linkToRoute('menu.dashboard.reports', 'fas fa-file-alt', 'dashboard', ['entityType' => 'report']),
            MenuItem::linkToRoute('menu.dashboard.systems', 'fas fa-cogs', 'dashboard', ['entityType' => 'system']),

            MenuItem::section('Sysstatus'),
            MenuItem::linkToCrud('menu.list.reports', 'fas fa-list', Report::class),
            MenuItem::linkToCrud('menu.list.systems', 'fas fa-cog', System::class),

            MenuItem::section('Konfiguration'),
            MenuItem::linkToCrud('menu.themes', 'fas fa-th-large', Theme::class),
            MenuItem::linkToCrud('menu.categories', 'fas fa-list', Category::class),

            MenuItem::section('Administration'),
            MenuItem::linkToCrud('Test', 'test', Answer::class),
            MenuItem::linkToCrud('User', 'fas fa-user', User::class),
            MenuItem::linkToCrud('Group', 'fas fa-users', Group::class),
            MenuItem::linkToCrud('Import kørsler ', 'fas fa-file-excel', ImportRun::class),
            MenuItem::linkToRoute('Eksport', 'fas fa-file-import', 'export_page'),

            MenuItem::section('Logout'),
            MenuItem::linkToLogout('Logout', 'fa fa-sign-out'),
        ];
    }
}
