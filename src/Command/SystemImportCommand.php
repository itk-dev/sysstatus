<?php

namespace App\Command;

use App\Entity\System;
use App\Service\SystemImporter;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class SystemImportCommand extends AbstractImportCommand
{
    public function __construct(SystemImporter $systemImporter, EntityManagerInterface $entityManager)
    {
        parent::__construct($systemImporter, $entityManager);
    }

    protected function configure(): void
    {
        $this
            ->setName('itstyr:import:system')
            ->setDescription('Import systems from System Portal feed.')
            ->addArgument('src', InputArgument::REQUIRED, 'The src of the feed.')
        ;
    }

    /**
     * @throws \Exception
     */
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $this->import(System::class, $input->getArgument('src'), $output);

        return 0;
    }
}
