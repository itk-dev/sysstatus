<?php

namespace App\Command;

use App\Entity\ImportRun;
use App\Entity\Report;
use App\Service\ReportImporter;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Input\InputArgument;

class ReportImportCommand extends Command
{
    private $reportImporter;
    private $entityManager;

    public function __construct(ReportImporter $reportImporter, EntityManagerInterface $entityManager)
    {
        parent::__construct();

        $this->reportImporter = $reportImporter;
        $this->entityManager = $entityManager;
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
        $success = true;
        $errorMessage = null;

        try {
            $this->reportImporter->import($input->getArgument('src'));
        } catch (\Exception $e) {
            $success = false;
            $errorMessage = $e->getMessage();
            $output->writeln($errorMessage);
        }

        $importRun = new ImportRun();
        $importRun->setDatetime(new \DateTime());
        $importRun->setOutput($success ? 'OK' : $errorMessage);
        $importRun->setResult($success);
        $importRun->setType(Report::class);

        $this->entityManager->persist($importRun);
        $this->entityManager->flush();
    }
}
