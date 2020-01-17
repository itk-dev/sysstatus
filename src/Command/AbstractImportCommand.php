<?php

namespace App\Command;

use App\Entity\ImportRun;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Command\Command;

abstract class AbstractImportCommand extends Command
{
    protected $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        parent::__construct();

        $this->entityManager = $entityManager;
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
