<?php

namespace App\Controller;

use App\Service\DataExporter;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\HttpFoundation\Request;

/**
 * Class ExportController
 * @package App\Controller
 */
class ExportController extends Controller
{
    /**
     * @Route("/export/report", name="export_report")
     *
     * @param \App\Service\DataExporter $dataExporter
     * @throws \PhpOffice\PhpSpreadsheet\Exception
     * @throws \PhpOffice\PhpSpreadsheet\Writer\Exception
     */
    public function exportReports(
        DataExporter $dataExporter
    ) {
        $dataExporter->exportReport();
    }

    /**
     * @Route("/export/system", name="export_system")
     *
     * @param \App\Service\DataExporter $dataExporter
     * @throws \PhpOffice\PhpSpreadsheet\Exception
     * @throws \PhpOffice\PhpSpreadsheet\Writer\Exception
     */
    public function exportSystems(
        DataExporter $dataExporter
    ) {
        $dataExporter->exportSystem();
    }

    /**
     * @Route("/export", name="export_page")
     *
     * @param \Symfony\Component\HttpFoundation\Request $request
     * @param \App\Service\DataExporter $dataExporter
     * @return \Symfony\Component\HttpFoundation\Response
     * @throws \PhpOffice\PhpSpreadsheet\Exception
     * @throws \PhpOffice\PhpSpreadsheet\Writer\Exception
     */
    public function exportPage(Request $request, DataExporter $dataExporter)
    {
        $groups = $this->get('doctrine.orm.default_entity_manager')->getRepository('App:Group')->findAll();

        $choices = [];

        foreach ($groups as $group) {
            $choices[$group->getName()] = $group->getId();
        }

        $form = $this->createFormBuilder()
            ->add('entity', ChoiceType::class, array('label' => 'Entitet', 'choices' => [
                'report' => 'report',
                'system' => 'system',
            ]))
            ->add('group', ChoiceType::class, array('label' => 'Gruppe', 'choices' => $choices))
            ->add('export_type', ChoiceType::class, [
                'label' => 'Eksport type',
                'choices' => [
                    'Resultater' => 'results',
                    'Kommentarer' => 'comments',
                ]
            ])
            ->add('color', ChoiceType::class, [
                'label' => 'Farve',
                'choices' => [
                    'Uden farver' => false,
                    'Med farver' => true,
                ],
            ])
            ->add('submit', SubmitType::class, array('label' => 'Hent'))
            ->getForm();

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $data = $form->getData();
            $selectedGroupId = $data['group'];
            $selectedEntity = $data['entity'];
            $selectedExportType = $data['export_type'];
            $withColor = $data['color'];

            if ($selectedEntity == 'report') {
                $dataExporter->exportReport($selectedGroupId, true, $selectedExportType === 'comments', $withColor);
            }
            else if ($selectedEntity == 'system') {
                $dataExporter->exportSystem($selectedGroupId, true, $selectedExportType === 'comments', $withColor);
            }
        }

        return $this->render('export.html.twig', [
            'groups' => $groups,
            'sort_form' => $form->createView(),
        ]);
    }
}
