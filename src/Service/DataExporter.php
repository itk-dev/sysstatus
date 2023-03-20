<?php

namespace App\Service;

use App\DBAL\Types\SmileyType;
use App\Entity\Answer;
use App\Entity\Category;
use App\Entity\Group;
use App\Entity\Report;
use App\Entity\System;
use App\Entity\Theme;
use App\Repository\ReportRepository;
use App\Repository\SystemRepository;
use App\Repository\ThemeRepository;
use Doctrine\Common\Collections\ArrayCollection;

use PhpOffice\PhpSpreadsheet\Shared\StringHelper;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Cell\Coordinate;
use Psr\Container\ContainerInterface;

/**
 * Class DataExporter.
 * @package App\Service
 */
class DataExporter
{
    protected $reportRepository;
    protected $systemRepository;
    protected $themeRepository;
    protected $basePath;

    /**
     * DataExporter constructor.
     *
     * @param ReportRepository $reportRepository
     * @param SystemRepository $systemRepository
     * @param ThemeRepository $themeRepository
     * @param ContainerInterface $container
     * @throws \Exception
     */
    public function __construct(
        ReportRepository $reportRepository,
        SystemRepository $systemRepository,
        ThemeRepository $themeRepository,
        ContainerInterface $container
    ) {
        $this->reportRepository = $reportRepository;
        $this->systemRepository = $systemRepository;
        $this->themeRepository = $themeRepository;

        $this->basePath = $container->get('kernel')->getProjectDir();
    }

    /**
     * Export reports or systems.
     *
     * @param string $filenamePrefix The filename prefix.
     * @param string $type The type of the entities that are exported.
     * @param array $entities The entities to export.
     * @param bool $splitIntoSubOwners Should the results be split into subowners?
     * @param bool $onlyComments Should the answer notes be displayed instead of results?
     * @param bool $withColor Whether or not colors should be displayed for answers.
     * @throws \PhpOffice\PhpSpreadsheet\Exception
     * @throws \PhpOffice\PhpSpreadsheet\Writer\Exception
     */
    public function export(
        $filenamePrefix,
        $type,
        $entities,
        $splitIntoSubOwners = false,
        $onlyComments = false,
        $withColor = false
    ) {
        $spreadsheet = new Spreadsheet();

        $filename = $filenamePrefix.'-'.date("Y-m-d-H_i_s");

        // If $splitIntoSubOwners is true, each entity that belongs to a subowner
        // will be gathered in its own work sheet in the spreadsheet.
        if (!$splitIntoSubOwners) {
            $sheet = $spreadsheet->getActiveSheet();
            $this->writeSheet($spreadsheet, $sheet, 0, $type, $entities,
                $onlyComments, $withColor);
        } else {
            $subOwnerEntities = [];

            foreach ($entities as $entity) {
                $subOwner = $entity->getSysOwnerSub();
                $subOwnerEntities[$subOwner][] = $entity;
            }

            $sheetNr = 0;

            // Create a WorkSheet for each subowner.
            foreach ($subOwnerEntities as $key => $entities) {
                $workSheet = null;
                if ($sheetNr > 0) {
                    $workSheet = $spreadsheet->addSheet(new Worksheet());
                } else {
                    $workSheet = $spreadsheet->getActiveSheet();
                }
                $nameKey = str_replace(['*', ':', '/', '\\', '?', '[', ']'], '',
                    $key);
                $workSheet->setTitle(StringHelper::substring($nameKey ?: 'No subowner',
                    0, Worksheet::SHEET_TITLE_MAXIMUM_LENGTH));

                $this->writeSheet($spreadsheet, $workSheet, $sheetNr, $type,
                    $entities, $onlyComments, $withColor);

                $sheetNr++;
            }
        }

        $spreadsheet->setActiveSheetIndex(0);

        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="'.$filename.'.xlsx"');
        header('Cache-Control: max-age=0');

        $writer = IOFactory::createWriter($spreadsheet, 'Xlsx');

        $writer->save('php://output');
        exit;
    }

    /**
     * Write $entities to a $sheet in the $spreadsheet.
     *
     * @param Spreadsheet $spreadsheet The spreadsheet.
     * @param Worksheet $sheet The worksheet to write to.
     * @param int $sheetIndex The index of the worksheet.
     * @param string $type Classname of the entities to export.
     * @param array $entities The entities.
     * @param bool $onlyComments If true only export comments for each answer.
     * @param bool $withColor If true set the answer color as background to each answer.
     * @throws \PhpOffice\PhpSpreadsheet\Exception
     */
    private function writeSheet(
        Spreadsheet $spreadsheet,
        Worksheet $sheet,
        int $sheetIndex,
        string $type,
        array $entities,
        bool $onlyComments = false,
        bool $withColor = false
    ) {
        $spreadsheet->setActiveSheetIndex($sheetIndex);

        $themesThatApply = [];
        $groupsThatApply = [];

        foreach ($entities as $entity) {
            foreach ($entity->getGroups() as $group) {
                $groupsThatApply[$group->getId()] = $group;
            }
        }

        /* @var Group $group */
        foreach ($groupsThatApply as $group) {
            $themes = $type == Report::class ? $group->getReportThemes() : $group->getSystemThemes();
            foreach ($themes as $theme) {
                $themesThatApply[$theme->getId()] = $theme;
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

        $categoryRow = ['Kategorier', '', '', '', ''];

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
        ];

        $headings = array_merge(
            $metaDataColumnHeadings,
            $answerColumns
        );

        $calculationColumnHeadings = [
            'Sum af svar',
            'Antal besvarede spørgsmål',
            'Resultat %',
        ];

        if (!$onlyComments) {
            $headings = array_merge(
                $headings,
                $calculationColumnHeadings
            );
        }

        foreach ($headings as $key => $cell) {
            $sheet->setCellValueByColumnAndRow($key + 1, 2, $cell);
        }

        $styleArray = [
            'font' => [
                'bold' => true,
            ],
        ];

        Coordinate::stringFromColumnIndex(count($categoryRow) + 1);

        $sheet->getStyle('A1:'.Coordinate::stringFromColumnIndex(count($categoryRow) + 1).'1')
            ->applyFromArray($styleArray)
            ->getAlignment()
            ->setTextRotation(45);
        $sheet->getStyle('A2:'.Coordinate::stringFromColumnIndex(count($headings) + 1).'2')
            ->applyFromArray($styleArray)
            ->getAlignment()
            ->setTextRotation(45);

        $rowNr = 2;

        foreach ($entities as $entity) {
            $rowNr++;
            $columnNr = count($metaDataColumnHeadings);

            $answers = $entity->getAnswers();

            $cellsThatApply = 0;

            // For each question, see if the entity has answer, and add it to
            // the row data.
            $questionColumns = [];
            $noteColumns = [];
            $colorColumns = [];
            foreach ($categories as $category) {
                $categoryApplies = $this->categoryAppliesToEntity($entity, $category);

                foreach ($category->getQuestions() as $question) {
                    $columnNr++;

                    $value = '';
                    $note = '';
                    $color = null;

                    if ($categoryApplies) {
                        foreach ($answers as $answer) {
                            if ($answer->getQuestion()
                                    ->getId() == $question->getId()) {
                                $value = $this->getAnswerValue($answer);
                                $note = $answer->getNote() ?: '';
                                $color = $this->getAnswerColor($answer);
                                break;
                            }
                        }

                        $cellsThatApply++;
                    }

                    $questionColumns[] = $value;
                    $noteColumns[] = $note;
                    $colorColumns[] = $color;
                }
            }

            // Calculation columns for the entity row.
            $calculationColumns = [];
            if (!$onlyComments) {
                if (count($metaDataColumnHeadings) < $columnNr - 1) {
                    $range = $this->getColumnLetter(count($metaDataColumnHeadings)).$rowNr.':'.
                        $this->getColumnLetter($columnNr - 1).$rowNr;

                    $calculationColumns = [
                        '=SUM('.$range.')',
                        '=COUNTIF('.$range.', 0)+COUNTIF('.$range.',1)+COUNTIF('.$range.',2)',
                        '=(('.$this->getColumnLetter($columnNr).$rowNr.' / 2)/'.$this->getColumnLetter($columnNr + 1).$rowNr.')* 100',
                    ];
                }
            }

            // Data about the entity.
            $metaColumns = [
                $entity->getSysInternalId(),
                $entity->getSysTitle(),
                $entity->getSysStatus(),
                implode(",", $entity->getGroups()->map(function (Group $group) {
                    return $group->getName();
                })->getValues()),
                $entity->getSysOwnerSub(),
            ];

            // Insert each cell for the entity row.
            foreach (array_merge(
                         $metaColumns,
                         $onlyComments ? $noteColumns : $questionColumns,
                         $calculationColumns
                     ) as $key => $cell) {
                $sheet->setCellValueByColumnAndRow($key + 1, $rowNr, $cell);
                $sheet->getStyleByColumnAndRow($key + 1, $rowNr)
                    ->getAlignment()
                    ->setWrapText(true);

                // Set color of cell.
                if ($withColor && $key >= count($metaColumns) && $key < count($questionColumns) + count($metaColumns)) {
                    $color = $colorColumns[$key - count($metaColumns)];

                    if ($color != null) {
                        $sheet->getStyleByColumnAndRow($key + 1, $rowNr, $cell)
                            ->getFill()
                            ->setFillType(Fill::FILL_SOLID)
                            ->getStartColor()
                            ->setARGB($color);
                        $sheet->getStyleByColumnAndRow($key + 1, $rowNr, $cell)
                            ->getFont()
                            ->getColor()
                            ->setARGB('ffffff');
                    }
                }
            }
        }

        // If the export is not for comments only, create summation rows in
        // the bottom of the worksheet.
        if (!$onlyComments) {
            // Count the number of questions.
            $nrOfQuestions = 0;
            foreach ($categories as $category) {
                $nrOfQuestions = $nrOfQuestions + count($category->getQuestions());
            }

            // Add bottom summations if questions have been set for the given entities.
            if ($nrOfQuestions > 0) {
                $columnRange = range(count($metaDataColumnHeadings) + 1,
                    count($metaDataColumnHeadings) + $nrOfQuestions);
                $rowNr = 2 + count($entities) + 2;

                // Insert row titles.
                $sheet->setCellValueByColumnAndRow(1, $rowNr,
                    $calculationColumnHeadings[0]);
                $sheet->setCellValueByColumnAndRow(1, $rowNr + 1,
                    $calculationColumnHeadings[1]);
                $sheet->setCellValueByColumnAndRow(1, $rowNr + 2,
                    $calculationColumnHeadings[2]);

                foreach ($columnRange as $column) {
                    $range = Coordinate::stringFromColumnIndex($column). 3 .':'.Coordinate::stringFromColumnIndex($column).($rowNr - 2);
                    $sheet->setCellValueByColumnAndRow($column, $rowNr,
                        '=SUM('.$range.')');
                    $sheet->setCellValueByColumnAndRow($column, $rowNr + 1,
                        '=COUNTIF('.$range.', 0)+COUNTIF('.$range.',1)+COUNTIF('.$range.',2)');
                    $sheet->setCellValueByColumnAndRow($column, $rowNr + 2,
                        '=IF('.Coordinate::stringFromColumnIndex($column).($rowNr + 1).'>0, (('.Coordinate::stringFromColumnIndex($column).($rowNr).' / 2)/'.Coordinate::stringFromColumnIndex($column).($rowNr + 1).')* 100, 0)');
                }
            }

        }

        // Add image describing values to the bottom of the excel sheet.
        $drawing = new \PhpOffice\PhpSpreadsheet\Worksheet\Drawing();
        $drawing->setName('Values');
        $drawing->setDescription('Values');
        $drawing->setPath($this->basePath.'/public/excel_values.jpg');
        $drawing->setCoordinates('A'.($rowNr + 4));
        $drawing->setWorksheet($sheet);
    }

    /**
     * Test if a category applies to an entity
     *
     * @param Report|System $entity
     * @param Category $category
     * @return bool
     */
    private function categoryAppliesToEntity($entity, $category)
    {
        $groupIds = $entity->getGroups()->map(function ($item) {
            return $item->getId();
        })->getValues();

        $entityClassName = get_class($entity);

        $categoryGroupIds = array_values(
            array_reduce($category->getThemes(), function ($carry, Theme $theme) use ($entityClassName) {
                $groups = [];
                if ($entityClassName == Report::class) {
                    $groups = $theme->getReportGroups();
                }
                else if ($entityClassName == System::class) {
                    $groups = $theme->getSystemGroups();
                }

                foreach ($groups as $group) {
                    $carry[$group->getId()] = $group->getId();
                }

                return $carry;
            }, [])
        );

        return array_intersect($groupIds, $categoryGroupIds) > 0;
    }

    /**
     * Get Excel column letter.
     *
     * @param $columnNr
     * @return string
     */
    private function getColumnLetter($columnNr)
    {
        $res = '';

        $range = range('A', 'Z');
        $countRange = count($range);

        if ($columnNr > $countRange) {
            $res = $range[intval($columnNr / $countRange) - 1];
        }

        $p = $columnNr % $countRange;

        $lastLetter = $range[$p];

        return $res.$lastLetter;
    }

    /**
     * Export reports.
     *
     * @param int|null $groupId The group id.
     * @param bool $splitIntoSubOwners Split the report into subowner worksheets.
     * @param bool $onlyComments Only export comments for each answer.
     * @param bool $withColor Show answer color as cell background.
     * @throws \PhpOffice\PhpSpreadsheet\Exception
     * @throws \PhpOffice\PhpSpreadsheet\Writer\Exception
     */
    public function exportReport(
        $groupId = null,
        $splitIntoSubOwners = false,
        $onlyComments = false,
        $withColor = false
    ) {
        $qb = $this->reportRepository->createQueryBuilder('e');
        $qb->where($qb->expr()->isNull('e.archivedAt'))
            ->andWhere($qb->expr()
                ->eq('e.sysStatus', $qb->expr()->literal('Aktiv')));

        if (isset($groupId)) {
            $qb->andWhere($qb->expr()->isMemberOf(':group', 'e.groups'))
                ->setParameter('group', $groupId);
        }

        $entities = $qb->getQuery()->execute();

        $this->export(
            'reports',
            Report::class,
            $entities,
            $splitIntoSubOwners,
            $onlyComments,
            $withColor
        );
    }

    /**
     * Export systems.
     *
     * @param int|null $groupId The group id.
     * @param bool $splitIntoSubOwners Split the report into subowner worksheets.
     * @param bool $onlyComments Only export comments for each answer.
     * @param bool $withColor Show answer color as cell background.
     * @throws \PhpOffice\PhpSpreadsheet\Exception
     * @throws \PhpOffice\PhpSpreadsheet\Writer\Exception
     */
    public function exportSystem(
        $groupId = null,
        $splitIntoSubOwners = false,
        $onlyComments = false,
        $withColor = false
    ) {
        $qb = $this->systemRepository->createQueryBuilder('e');
        $qb->where($qb->expr()->isNull('e.archivedAt'))
            ->andWhere($qb->expr()
                ->neq('e.sysStatus',
                    $qb->expr()->literal('Systemet bruges ikke længere')));

        if (isset($groupId)) {
            $qb->andWhere($qb->expr()->isMemberOf(':group', 'e.groups'))
                ->setParameter('group', $groupId);
        }

        $entities = $qb->getQuery()->execute();

        $this->export(
            'systems',
            System::class,
            $entities,
            $splitIntoSubOwners,
            $onlyComments,
            $withColor
        );
    }

    /**
     * Get a value corresponding to answer smiley selected.
     *
     * @param \App\Entity\Answer $answer
     * @return int
     */
    private function getAnswerValue(Answer $answer)
    {
        switch ($answer->getSmiley()) {
            case SmileyType::BLUE:
            case SmileyType::GREEN:
                return 2;
            case SmileyType::YELLOW:
                return 1;
            case SmileyType::RED:
            default:
                return 0;
        }
    }

    /**
     * Get color depending on answer smiley value.
     *
     * @param \App\Entity\Answer $answer
     * @return string|null
     */
    private function getAnswerColor(Answer $answer)
    {
        switch ($answer->getSmiley()) {
            case SmileyType::BLUE:
                return '3661D8';
            case SmileyType::GREEN:
                return '008855';
            case SmileyType::RED:
                return 'D32F2F';
            case SmileyType::YELLOW:
                return 'F6BD1D';
            default:
                return null;
        }
    }
}
