<?php

namespace App\Command;

use App\Entity\Report;
use App\Service\ReportImporter;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Input\InputArgument;

class ReportImportCommand extends AbstractImportCommand
{
    public function __construct(ReportImporter $reportImporter, EntityManagerInterface $entityManager)
    {
        parent::__construct($reportImporter, $entityManager);
    }

    protected function configure()
    {
        $this
            ->setName('itstyr:import:report')
            ->setDescription('Import reports from feed.')
            ->addArgument('src', InputArgument::REQUIRED, 'The src of the feed.');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->import(Report::class, $input->getArgument('src'), $output);

        return 0;
    }
}
