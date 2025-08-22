<?php

namespace App\DataFixtures;

use App\Service\ReportImporter;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Bundle\FixturesBundle\FixtureGroupInterface;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\HttpKernel\KernelInterface;

class ReportFixtures extends Fixture implements FixtureGroupInterface
{
    public function __construct(protected ReportImporter $reportImporter, protected KernelInterface $kernel)
    {
    }

    public function load(ObjectManager $manager): void
    {
        $rootPath = $this->kernel->getProjectDir();
        $this->reportImporter->import($rootPath.'/example_data/reports.json');
    }

    public static function getGroups(): array
    {
        return ['imported_data'];
    }
}
