<?php

namespace App\Command;

use App\Service\ReportImporter;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Input\InputArgument;

class ReportImportCommand extends Command
{
    private $reportImporter;

    public function __construct(ReportImporter $reportImporter)
    {
        parent::__construct();

        $this->reportImporter = $reportImporter;
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
        $this->reportImporter->import($input->getArgument('src'));
    }
}
