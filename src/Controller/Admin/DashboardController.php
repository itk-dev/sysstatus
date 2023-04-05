<?php

namespace App\Controller\Admin;

use App\Entity\Category;
use App\Entity\Group;
use App\Entity\ImportRun;
use App\Entity\Report;
use App\Entity\System;
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

//    #[Route('/admin', name: 'admin')]
     public function index(): Response
    {
       // return parent::index();

        // Option 1. You can make your dashboard redirect to some common page of your backend

         $adminUrlGenerator = $this->container->get(AdminUrlGenerator::class);
         return $this->redirect($adminUrlGenerator->setController(AdminContent::class)->generateUrl());

        // Option 2. You can make your dashboard redirect to different pages depending on the user
        //
        // if ('jane' === $this->getUser()->getUsername()) {
        //     return $this->redirect('...');
        // }

        // Option 3. You can render some custom template to display a proper dashboard with widgets, etc.
        // (tip: it's easier if your template extends from @EasyAdmin/page/content.html.twig)
        //
        // return $this->render('some/path/my-dashboard.html.twig');
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
            // ...

            // the first argument is the "template name", which is the same as the
            // Twig path but without the `@EasyAdmin/` prefix
            ->overrideTemplate('label/null', 'easy_admin_overrides/label_null.html.twig');
//
//            ->overrideTemplates([
//                'crud/index' => 'admin/pages/index.html.twig',
//                'crud/field/textarea' => 'admin/fields/dynamic_textarea.html.twig',
//            ]);
    }



    public function configureMenuItems(): iterable
    {
//        yield MenuItem::linkToDashboard('Dashboard', 'fa fa-home');
//
//        MenuItem::section('Blog');
//        yield MenuItem::linkToCrud('User', 'fas fa-user', User::class);
//        yield MenuItem::linkToCrud('menu.dashboard.systems', 'fas fa-cogs', System::class);
//
//        MenuItem::section('Blog');
//        yield MenuItem::linkToCrud('menu.categories', 'fas fa-list', Category::class);
//        // yield MenuItem::linkToCrud('The Label', 'fas fa-list', EntityClass::class);

        return [



            MenuItem::section('Dashboard '),
//                MenuItem::linkToDashboard('Dashboard-Systemer', 'fa fa-cog'),
//                MenuItem::linkToDashboard('Dashboard-Anmeldelser', 'fa fa-smiley', Report::class),
                  MenuItem::linkToCrud('Dashboard-Systems', 'fa fa-cog', System::class)->setController(CustomSystemDashboard::class),


            MenuItem::section('Sysstatus'),
                MenuItem::linkToCrud('Systemer', 'fas fa-cogs', System::class),

            MenuItem::section('Konfiguration'),
                MenuItem::linkToCrud('menu.categories', 'fas fa-list', Category::class),

            MenuItem::section('Administration'),
                MenuItem::linkToCrud('User', 'fas fa-user', User::class),
                MenuItem::linkToCrud('Group', 'fas fa-users', Group::class),
                MenuItem::linkToCrud('Import kørsler ', 'fas fa-file-excel', ImportRun::class),
                MenuItem::linkToRoute('Eksport', 'fas fa-file-import', 'export_page'),



//            MenuItem::section('Administration'),
//                MenuItem::linkToCrud('menu.categories', 'fas fa-list', Category::class),
//            MenuItem::linkToCrud('The Label', 'fas fa-list', EntityClass::class);

        ];

    }
}
