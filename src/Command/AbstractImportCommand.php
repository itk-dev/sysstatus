<?php

namespace App\Command;

use App\Entity\ImportRun;
use App\Service\BaseImporter;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Output\OutputInterface;

abstract class AbstractImportCommand extends Command
{
    protected $entityManager;
    protected $importer;

    public function __construct(BaseImporter $importer, EntityManagerInterface $entityManager)
    {
        parent::__construct();

        $this->entityManager = $entityManager;
        $this->importer = $importer;
    }

    /**
     * Run the importer.
     *
     * @param string $type
     * @param string $src
     * @param \Symfony\Component\Console\Output\OutputInterface $output
     * @throws \Exception
     */
    protected function import(string $type, string $src, OutputInterface $output)
    {
        $success = true;
        $errorMessage = null;

        try {
            $this->importer->import($src);
        } catch (\Exception $e) {
            $success = false;
            $errorMessage = $e->getMessage();
            $output->writeln($errorMessage);
        }

        $this->recordImportRun($type, $success, $errorMessage);
    }

    /**
     * Record import run.
     *
     * @param string $type
     *   The type of the import
     * @param bool $success
     *   Success of run
     * @param string|null $output
     *   Output message or null
     * @throws \Exception
     */
    protected function recordImportRun(string $type, bool $success, string $output = null) {
        $importRun = new ImportRun();
        $importRun->setDatetime(new \DateTime());
        $importRun->setOutput($output);
        $importRun->setResult($success);
        $importRun->setType($type);

        $this->entityManager->persist($importRun);
        $this->entityManager->flush();
    }
}
