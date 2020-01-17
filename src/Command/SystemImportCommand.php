<?php

namespace App\Command;

use App\Entity\ImportRun;
use App\Entity\System;
use App\Service\SystemImporter;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Input\InputArgument;

class SystemImportCommand extends Command
{
    private $systemImporter;
    private $entityManager;

    public function __construct(SystemImporter $systemImporter, EntityManagerInterface $entityManager)
    {
        parent::__construct();

        $this->systemImporter = $systemImporter;
        $this->entityManager = $entityManager;
    }

    protected function configure()
    {
        $this
            ->setName('itstyr:import:system')
            ->setDescription('Import systems from System Portal feed.')
            ->addArgument('src', InputArgument::REQUIRED, 'The src of the feed.');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $success = true;
        $errorMessage = null;

        try {
            $this->systemImporter->import($input->getArgument('src'));
        } catch (\Exception $e) {
            $success = false;
            $errorMessage = $e->getMessage();
            $output->writeln($errorMessage);
        }

        $importRun = new ImportRun();
        $importRun->setDatetime(new \DateTime());
        $importRun->setOutput($success ? 'OK' : $errorMessage);
        $importRun->setResult($success);
        $importRun->setType(System::class);

        $this->entityManager->persist($importRun);
        $this->entityManager->flush();
    }
}
