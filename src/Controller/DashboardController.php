<?php

namespace App\Controller;

use App\Repository\ReportRepository;
use App\Repository\SystemRepository;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class DashboardController extends Controller
{
    /**
     * @Route("/dashboard/reports", name="dashboard_reports")
     */
    public function reports(ReportRepository $reportRepository)
    {
        $reports = $reportRepository->findAll();

        return $this->render('dashboard/index.html.twig', [
            'entities' => $reports,
            'controller_name' => 'DashboardController',
        ]);
    }

    /**
     * @Route("/dashboard/systems", name="dashboard_systems")
     */
    public function systems(SystemRepository $systemRepository)
    {
        $systems = $systemRepository->findAll();

        return $this->render('dashboard/index.html.twig', [
            'entities' => $systems,
            'controller_name' => 'DashboardController',
        ]);
    }
}
