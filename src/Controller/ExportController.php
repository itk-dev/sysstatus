<?php

namespace App\Controller;

use App\Entity\Answer;
use App\Repository\AnswerRepository;
use App\Repository\QuestionRepository;
use App\Repository\ReportRepository;
use App\Repository\SystemRepository;
use App\Service\DataExporter;
use Box\Spout\Common\Type;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

class ExportController extends Controller
{
    /**
     * @Route("/export/report", name="export_report")
     */
    public function exportReports(
        DataExporter $dataExporter
    ) {
        return $dataExporter->exportReport();
    }

    /**
     * @Route("/export/system", name="export_system")
     */
    public function exportSystems(
        DataExporter $dataExporter
    ) {
        return $dataExporter->exportSystem();
    }

    /**
     * @Route("/export", name="export_page")
     */
    public function exportPage()
    {
        return $this->render('export.html.twig', [

        ]);
    }
}
