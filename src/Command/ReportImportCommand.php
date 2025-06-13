<?php

namespace App\Command;

use App\Entity\Report;
use App\Service\ReportImporter;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class ReportImportCommand extends AbstractImportCommand
{
    public function __construct(ReportImporter $reportImporter, EntityManagerInterface $entityManager)
    {
        parent::__construct($reportImporter, $entityManager);
    }

    protected function configure(): void
    {
        $this
            ->setName('itstyr:import:report')
            ->setDescription('Import reports from feed.')
            ->addArgument('src', InputArgument::REQUIRED, 'The src of the feed.')
        ;
    }

    /**
     * @throws \Exception
     */
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $this->import(Report::class, $input->getArgument('src'), $output);

        return 0;
    }
}
