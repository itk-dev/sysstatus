<?php

namespace App\Service;

use App\DBAL\Types\SmileyType;
use App\Repository\CategoryRepository;
use App\Repository\ReportRepository;
use App\Repository\SystemRepository;
use Box\Spout\Writer\WriterFactory;
use Box\Spout\Common\Type;
use Box\Spout\Writer\Style\StyleBuilder;

class DataExporter
{
    protected $reportRepository;
    protected $systemRepository;
    protected $categoryRepository;

    /**
     * DataExporter constructor.
     */
    public function __construct(ReportRepository $reportRepository, SystemRepository $systemRepository, CategoryRepository $categoryRepository)
    {
        $this->reportRepository = $reportRepository;
        $this->systemRepository = $systemRepository;
        $this->categoryRepository = $categoryRepository;
    }

    /**
     * Export reports.
     *
     * @throws \Box\Spout\Common\Exception\IOException
     * @throws \Box\Spout\Common\Exception\InvalidArgumentException
     * @throws \Box\Spout\Common\Exception\UnsupportedTypeException
     * @throws \Box\Spout\Writer\Exception\WriterNotOpenedException
     */
    public function exportReport() {
        $writer = WriterFactory::create(Type::XLSX); // for XLSX files

        $writer->openToBrowser('reports-'.date("Y-m-d-H:i:s").'.xlsx');

        $reports = $this->reportRepository->findAll();

        $style = (new StyleBuilder())
            ->setFontBold()
            ->setFontSize(15)
            ->setShouldWrapText()
            ->build();

        $categories = $this->categoryRepository->findAll();

        $answerColumns = [];

        $categoryRow = ['Kategori', '', '', '', ''];

        foreach ($categories as $category) {
            $categoryRow[] = $category->getName();

            foreach ($category->getQuestions() as $question) {
                $categoryRow[] = '';
                $answerColumns[] = $question->getQuestion();
            }
        }

        $writer->addRowWithStyle($categoryRow, $style);

        $writer->addRowWithStyle(array_merge([
            'Id',
            'Navn',
            'Status',
            'Gruppe',
            'Afdeling',
        ], $answerColumns), $style);

        foreach ($reports as $report) {
            $answers = $report->getAnswers();

            $questionColumns = [];

            foreach ($categories as $category) {
                foreach ($category->getQuestions() as $question) {
                    $value = '';

                    foreach ($answers as $answer) {
                        if ($answer->getQuestion()->getId() == $question->getId()) {
                            if ($answer->getSmiley() == SmileyType::BLUE ||
                                $answer->getSmiley() == SmileyType::GREEN) {
                                $value = 2;
                            }
                            else if ($answer->getSmiley() == SmileyType::YELLOW)  {
                                $value = 1;
                            }
                            else if ($answer->getSmiley() == SmileyType::RED ||
                                is_null($answer->getSmiley())
                            ) {
                                $value = 0;
                            }

                            break;
                        }
                    }

                    $questionColumns[] = $value;
                }
            }

            $writer->addRow(array_merge([
                $report->getSysInternalId(),
                $report->getSysTitle(),
                $report->getSysStatus(),
                $report->getGroup()->getName(),
                $report->getSysOwnerSub()
            ], $questionColumns));
        }

        $writer->close();
        // https://github.com/box/spout/issues/168#issuecomment-197276897
        exit;
    }

    /**
     * Export system answers.
     *
     * @throws \Box\Spout\Common\Exception\IOException
     * @throws \Box\Spout\Common\Exception\InvalidArgumentException
     * @throws \Box\Spout\Common\Exception\UnsupportedTypeException
     * @throws \Box\Spout\Writer\Exception\WriterNotOpenedException
     */
    public function exportSystem() {
        $writer = WriterFactory::create(Type::XLSX); // for XLSX files

        $writer->openToBrowser('systems-'.date("Y-m-d-H:i:s").'.xlsx');

        $systems = $this->systemRepository->findAll();

        $style = (new StyleBuilder())
            ->setFontBold()
            ->setFontSize(15)
            ->setShouldWrapText()
            ->build();

        $categories = $this->categoryRepository->findAll();

        $answerColumns = [];

        $categoryRow = ['Kategori', '', '', '', ''];

        foreach ($categories as $category) {
            $categoryRow[] = $category->getName();

            foreach ($category->getQuestions() as $question) {
                $categoryRow[] = '';
                $answerColumns[] = $question->getQuestion();
            }
        }

        $writer->addRowWithStyle($categoryRow, $style);

        $writer->addRowWithStyle(array_merge([
            'ID',
            'Navn',
            'Status',
            'Gruppe',
            'Afdeling',
        ], $answerColumns), $style);

        foreach ($systems as $system) {
            $answers = $system->getAnswers();

            $questionColumns = [];

            foreach ($categories as $category) {
                foreach ($category->getQuestions() as $question) {
                    $value = '';

                    foreach ($answers as $answer) {
                        if ($answer->getQuestion()->getId() == $question->getId()) {
                            if ($answer->getSmiley() == SmileyType::BLUE ||
                                $answer->getSmiley() == SmileyType::GREEN) {
                                $value = 2;
                            }
                            else if ($answer->getSmiley() == SmileyType::YELLOW)  {
                                $value = 1;
                            }
                            else if ($answer->getSmiley() == SmileyType::RED ||
                                is_null($answer->getSmiley())
                            ) {
                                $value = 0;
                            }

                            break;
                        }
                    }

                    $questionColumns[] = $value;
                }
            }

            $writer->addRow(array_merge([
                $system->getSysInternalId(),
                $system->getSysTitle(),
                $system->getSysStatus(),
                $system->getGroup()->getName(),
                $system->getSysOwnerSub()
            ], $questionColumns));
        }

        $writer->close();
        // https://github.com/box/spout/issues/168#issuecomment-197276897
        exit;
    }
}
