<?php

namespace App\Command;

use App\Service\SystemImporter;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Input\InputArgument;

class SystemImportCommand extends Command
{
    private $systemImporter;

    public function __construct(SystemImporter $systemImporter)
    {
        parent::__construct();

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
        $this->systemImporter->import($input->getArgument('src'));
    }
}
