<?php

namespace App\Service;

use App\DBAL\Types\SmileyType;
use App\Entity\Report;
use App\Entity\System;
use App\Repository\ReportRepository;
use App\Repository\SystemRepository;
use App\Repository\ThemeRepository;
use Doctrine\Common\Collections\ArrayCollection;

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class DataExporter
{
    protected $reportRepository;
    protected $systemRepository;
    protected $themeRepository;

    /**
     * DataExporter constructor.
     *
     * @param \App\Repository\ReportRepository $reportRepository
     * @param \App\Repository\SystemRepository $systemRepository
     * @param \App\Repository\ThemeRepository $themeRepository
     */
    public function __construct(
        ReportRepository $reportRepository,
        SystemRepository $systemRepository,
        ThemeRepository $themeRepository
    ) {
        $this->reportRepository = $reportRepository;
        $this->systemRepository = $systemRepository;
        $this->themeRepository = $themeRepository;
    }

    public function export(
        $typeString,
        $type,
        $entities,
        $splitIntoSubOwners = false
    ) {
        $spreadsheet = new Spreadsheet();

        $filename = $typeString.'-'.date("Y-m-d-H_i_s");

        if (!$splitIntoSubOwners) {
            $sheet = $spreadsheet->getActiveSheet();
            $this->writeSheet($spreadsheet, $sheet, 0 ,$type, $entities);
        }
        else {
            $subOwnerEntities = [];

            foreach ($entities as $entity) {
                $subOwner = $entity->getSysOwnerSub();
                $subOwnerEntities[$subOwner][] = $entity;
            }

            $sheetNr = 0;

            foreach ($subOwnerEntities as $key => $entities) {
                $workSheet = null;
                if ($sheetNr > 0) {
                    $workSheet = $spreadsheet->addSheet(new Worksheet());
                }
                else {
                    $workSheet = $spreadsheet->getActiveSheet();
                }
                $workSheet->setTitle($key ?: 'No subowner');
                $spreadsheet->setActiveSheetIndex($sheetNr);

                $this->writeSheet($spreadsheet, $workSheet, $sheetNr, $type, $entities);

                $sheetNr++;
            }
        }

        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="'.$filename.'.xlsx"');
        header('Cache-Control: max-age=0');

        $writer = IOFactory::createWriter($spreadsheet, 'Xlsx');

        $writer->save('php://output');
        exit;
    }

    private function writeSheet($spreadsheet, $sheet, $page, $type, $entities) {
        $spreadsheet->setActiveSheetIndex($page);

        $themesThatApply = [];

        // Find themes that is attached to the entities.
        $themes = $this->themeRepository->findAll();
        foreach ($themes as $theme) {
            if ($type == Report::class) {
                if (count($theme->getReports()) > 0 &&
                    count(array_intersect(
                        $theme->getReports()->toArray(),
                        $entities
                    )) > 0
                ) {
                    $themesThatApply[] = $theme;
                }
            } else {
                if ($type == System::class) {
                    if (count($theme->getSystems()) > 0 &&
                        count(array_intersect(
                            $theme->getSystems()->toArray(),
                            $entities->toArray()
                        )) > 0
                    ) {
                        $themesThatApply[] = $theme;
                    }
                }
            }
        }

        $categories = new ArrayCollection();

        foreach ($themesThatApply as $theme) {
            foreach ($theme->getThemeCategories() as $themeCategory) {
                $category = $themeCategory->getCategory();
                if (!$categories->contains($category)) {
                    $categories->add($category);
                }
            }
        }

        $answerColumns = [];

        $categoryRow = ['Kategorier', '', '', '', '', ''];

        foreach ($categories as $category) {
            $categoryRow[] = $category->getName();
            $setCategory = true;

            if (count($category->getQuestions()) === 0) {
                $answerColumns[] = '';
            } else {
                foreach ($category->getQuestions() as $question) {
                    if (!$setCategory) {
                        $categoryRow[] = '';
                    }
                    $answerColumns[] = $question->getQuestion();
                    $setCategory = false;
                }
            }
        }

        foreach ($categoryRow as $key => $cell) {
            $sheet->setCellValueByColumnAndRow($key + 1, 1, $cell);
        }

        $metaDataColumnHeadings = [
            'Id',
            'Navn',
            'Status',
            'Gruppe',
            'Afdeling',
            'Tema',
        ];

        $calculationColumnHeadings = [
            'Sum af svar',
            'Antal spørgsmål',
            'Resultat %'
        ];

        $headings = array_merge(
            $metaDataColumnHeadings,
            $answerColumns,
            $calculationColumnHeadings
        );

        foreach ($headings as $key => $cell) {
            $sheet->setCellValueByColumnAndRow($key + 1, 2, $cell);
        }

        $styleArray = [
            'font' => [
                'bold' => true,
            ],
        ];

        $sheet->getStyle('A1:'. $this->getColumnLetter(count($categoryRow) + 1) . '1')->applyFromArray($styleArray)->getAlignment()->setTextRotation(45);
        $sheet->getStyle('A2:'. $this->getColumnLetter(count($headings) + 1) . '2')->applyFromArray($styleArray)->getAlignment()->setTextRotation(45);

        $rowNr = 2;

        foreach ($entities as $entity) {
            $rowNr++;
            $columnNr = count($metaDataColumnHeadings);

            $answers = $entity->getAnswers();

            $questionColumns = [];

            $cellsThatApply = 0;

            foreach ($categories as $category) {
                $categoryApplies = $this->categoryAppliesToEntity($entity, $category);

                foreach ($category->getQuestions() as $question) {
                    $columnNr++;

                    $value = '';

                    if ($categoryApplies) {
                        foreach ($answers as $answer) {
                            if ($answer->getQuestion()->getId() == $question->getId(
                                )) {
                                if ($answer->getSmiley() == SmileyType::BLUE ||
                                    $answer->getSmiley() == SmileyType::GREEN) {
                                    $value = 2;
                                } else {
                                    if ($answer->getSmiley(
                                        ) == SmileyType::YELLOW) {
                                        $value = 1;
                                    } else {
                                        if ($answer->getSmiley(
                                            ) == SmileyType::RED ||
                                            is_null($answer->getSmiley())
                                        ) {
                                            $value = 0;
                                        }
                                    }
                                }

                                break;
                            }
                        }

                        if ($value == '') {
                            $value = 0;
                        }

                        $cellsThatApply++;
                    }

                    $questionColumns[] = $value;
                }
            }

            $calculationColumns = [
                '=SUM(' . $this->getColumnLetter(count($metaDataColumnHeadings)) . $rowNr . ':' .
                $this->getColumnLetter($columnNr - 1) . $rowNr . ')',
                $cellsThatApply,
                '=((' . $this->getColumnLetter($columnNr) . $rowNr . ' / 2)/' . $this->getColumnLetter($columnNr + 1) . $rowNr . ')* 100'
            ];

            foreach (array_merge(
                         [
                             $entity->getSysInternalId(),
                             $entity->getSysTitle(),
                             $entity->getSysStatus(),
                             $entity->getGroup()->getName(),
                             $entity->getSysOwnerSub(),
                             $entity->getTheme(),
                         ],
                         $questionColumns,
                         $calculationColumns
                     ) as $key => $cell) {
                $sheet->setCellValueByColumnAndRow($key + 1, $rowNr, $cell);
            }
        }
    }

    /**
     * Test if a category applies to an entity
     *
     * @param $entity
     * @param $category
     * @return bool
     */
    private function categoryAppliesToEntity($entity, $category) {
        $theme = $entity->getTheme();

        if (is_null($theme)) {
            return false;
        }

        foreach ($theme->getThemeCategories() as $themeCategory) {
            if ($themeCategory->getCategory() == $category) {
                return true;
            }
        }
        return false;
    }

    /**
     * Get Excel column letter.
     *
     * @param $columnNr
     * @return string
     */
    private function getColumnLetter($columnNr) {
        $res = '';

        $range = range('A','Z');
        $countRange = count($range);

        if ($columnNr > $countRange) {
            $res = $range[intval($columnNr / $countRange) - 1];
        }

        $p = $columnNr % $countRange;

        $lastLetter = $range[$p];

        return $res . $lastLetter;
    }

    /**
     * Export reports.
     *
     * @param null $groupId
     */
    public function exportReport($groupId = null, $splitIntoSubOwners = false)
    {
        $entities = null;

        if (isset($groupId)) {
            $entities = $this->reportRepository->findBy(['group' => $groupId, 'sysStatus' => 'Aktiv']);
        }
        else {
            $entities = $this->reportRepository->findAll();
        }

        $this->export(
            'reports',
            Report::class,
            $entities,
            $splitIntoSubOwners
        );
    }

    /**
     * Export systems.
     */
    public function exportSystem($groupId = null, $splitIntoSubOwners = false)
    {
        $entities = null;

        if (isset($groupId)) {
            $entities = $this->systemRepository->createQueryBuilder('s')
            ->where('s.group = :group')
            ->setParameter('group', $groupId)
            ->andWhere('s.sysStatus != \'Systemet bruges ikke længere\'')
            ->getQuery()->getResult();

            //$entities = $this->systemRepository->findBy(['group' => $groupId, 'sysStatus' => 'Systemet bruges ikke længere']);
        }
        else {
            $entities = $this->systemRepository->findAll();
        }

        $this->export(
            'systems',
            System::class,
            $entities,
            $splitIntoSubOwners
        );
    }
}
