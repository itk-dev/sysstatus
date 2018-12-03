<?php

namespace App\Service;

use App\DBAL\Types\SmileyType;
use App\Entity\Report;
use App\Entity\System;
use App\Repository\ReportRepository;
use App\Repository\SystemRepository;
use App\Repository\ThemeRepository;
use Box\Spout\Writer\WriterFactory;
use Box\Spout\Common\Type;
use Box\Spout\Writer\Style\StyleBuilder;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\EntityRepository;

class DataExporter
{
    protected $reportRepository;
    protected $systemRepository;
    protected $themeRepository;
    protected $headerStyle;

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
        $this->headerStyle = (new StyleBuilder())
            ->setFontBold()
            ->setShouldWrapText(true)
            ->build();
    }

    /**
     * Export an excel file of the given entity type.
     *
     * @param \Doctrine\ORM\EntityRepository $entityRepository
     * @param $typeString
     * @param $type
     * @throws \Box\Spout\Common\Exception\IOException
     * @throws \Box\Spout\Common\Exception\InvalidArgumentException
     * @throws \Box\Spout\Common\Exception\UnsupportedTypeException
     * @throws \Box\Spout\Writer\Exception\WriterNotOpenedException
     */
    public function exportEntities(
        EntityRepository $entityRepository,
        $typeString,
        $type
    ) {
        $writer = WriterFactory::create(Type::XLSX); // for XLSX files

        $entities = $entityRepository->findAll();

        $writer->openToBrowser($typeString.'-'.date("Y-m-d-H_i_s").'.xlsx');

        $themesThatApply = [];

        $themes = $this->themeRepository->findAll();
        foreach ($themes as $theme) {
            if ($type == Report::class) {
                if (count($theme->getReports()) > 0) {
                    $themesThatApply[] = $theme;
                }
            } else {
                if ($type == System::class) {
                    if (count($theme->getSystems()) > 0) {
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

        $writer->addRowWithStyle($categoryRow, $this->headerStyle);

        $writer->addRowWithStyle(
            array_merge(
                [
                    'Id',
                    'Navn',
                    'Status',
                    'Gruppe',
                    'Afdeling',
                ],
                $answerColumns
            ),
            $this->headerStyle
        );

        foreach ($entities as $entity) {
            $answers = $entity->getAnswers();

            $questionColumns = [];

            foreach ($categories as $category) {
                foreach ($category->getQuestions() as $question) {
                    $value = '';

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

                    $questionColumns[] = $value;
                }
            }

            $writer->addRow(
                array_merge(
                    [
                        $entity->getSysInternalId(),
                        $entity->getSysTitle(),
                        $entity->getSysStatus(),
                        $entity->getGroup()->getName(),
                        $entity->getSysOwnerSub(),
                    ],
                    $questionColumns
                )
            );
        }

        $writer->close();
        // https://github.com/box/spout/issues/168#issuecomment-197276897
        exit;
    }

    /**
     * Export reports.
     */
    public function exportReport()
    {
        $this->exportEntities(
            $this->reportRepository,
            'reports',
            Report::class
        );
    }

    /**
     * Export systems.
     */
    public function exportSystem()
    {
        $this->exportEntities(
            $this->systemRepository,
            'systems',
            System::class
        );
    }
}
