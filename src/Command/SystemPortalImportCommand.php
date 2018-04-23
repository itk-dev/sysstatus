<?php

namespace App\Command;

use App\Service\SystemPortalImporter;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Input\InputArgument;

class SystemPortalImportCommand extends Command
{
    private $systemPortalImporter;

    public function __construct(SystemPortalImporter $systemPortalImporter)
    {
        parent::__construct();

        $this->systemPortalImporter = $systemPortalImporter;
    }

    protected function configure()
    {
        $this
            ->setName('itstyr:import')
            ->setDescription('Import systems from System Portal feed.')
            ->addArgument('src', InputArgument::REQUIRED, 'The src of the feed.');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->systemPortalImporter->import($input->getArgument('src'));
    }
}
