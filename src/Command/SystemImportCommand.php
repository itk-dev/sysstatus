<?php

namespace App\Command;

use App\Entity\System;
use App\Service\SystemImporter;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Input\InputArgument;

class SystemImportCommand extends AbstractImportCommand
{
    private $systemImporter;

    public function __construct(SystemImporter $systemImporter, EntityManagerInterface $entityManager)
    {
        parent::__construct($entityManager);

        $this->systemImporter = $systemImporter;
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

        $this->recordImportRun(System::class, $success, $errorMessage);
    }
}
