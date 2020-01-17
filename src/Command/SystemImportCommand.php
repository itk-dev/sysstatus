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
    public function __construct(SystemImporter $systemImporter, EntityManagerInterface $entityManager)
    {
        parent::__construct($systemImporter, $entityManager);
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
        $this->import(System::class, $input->getArgument('src'), $output);
    }
}
