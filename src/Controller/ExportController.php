<?php

namespace App\Controller;

use App\Repository\GroupRepository;
use App\Service\DataExporter;
use PhpOffice\PhpSpreadsheet\Exception;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

/**
 * Class ExportController.
 */
class ExportController extends AbstractController
{
    /**
     * @throws Exception
     * @throws \PhpOffice\PhpSpreadsheet\Writer\Exception
     */
    #[Route(path: '/export/report', name: 'export_report')]
    public function exportReports(DataExporter $dataExporter): void
    {
        $dataExporter->exportReport();
    }

    /**
     * @throws Exception
     * @throws \PhpOffice\PhpSpreadsheet\Writer\Exception
     */
    #[Route(path: '/export/system', name: 'export_system')]
    public function exportSystems(DataExporter $dataExporter): void
    {
        $dataExporter->exportSystem();
    }

    /**
     * @throws Exception
     * @throws \PhpOffice\PhpSpreadsheet\Writer\Exception
     */
    #[Route(path: '/export', name: 'export_page')]
    public function exportPage(Request $request, DataExporter $dataExporter, GroupRepository $groupRepository): Response
    {
        $groups = $groupRepository->findAll();

        $choices = [];

        foreach ($groups as $group) {
            $choices[$group->getName()] = $group->getId();
        }

        $form = $this->createFormBuilder()
            ->add('entity', ChoiceType::class, ['label' => 'Entitet', 'choices' => [
                'report' => 'report',
                'system' => 'system',
            ]])
            ->add('group', ChoiceType::class, ['label' => 'Gruppe', 'choices' => $choices])
            ->add('export_type', ChoiceType::class, [
                'label' => 'Eksport type',
                'choices' => [
                    'Resultater' => 'results',
                    'Kommentarer' => 'comments',
                ],
            ])
            ->add('color', ChoiceType::class, [
                'label' => 'Farve',
                'choices' => [
                    'Uden farver' => false,
                    'Med farver' => true,
                ],
            ])
            ->add('submit', SubmitType::class, ['label' => 'Hent'])
            ->getForm()
        ;

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $data = $form->getData();
            $selectedGroupId = $data['group'];
            $selectedEntity = $data['entity'];
            $selectedExportType = $data['export_type'];
            $withColor = $data['color'];

            if ('report' == $selectedEntity) {
                $dataExporter->exportReport($selectedGroupId, true, 'comments' === $selectedExportType, $withColor);
            } elseif ('system' == $selectedEntity) {
                $dataExporter->exportSystem($selectedGroupId, true, 'comments' === $selectedExportType, $withColor);
            }
        }

        return $this->render('export.html.twig', [
            'groups' => $groups,
            'sort_form' => $form->createView(),
        ]);
    }
}
