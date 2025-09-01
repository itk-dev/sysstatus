<?php

namespace App\DataFixtures;

use App\Service\ReportImporter;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Bundle\FixturesBundle\FixtureGroupInterface;
use Doctrine\Persistence\ObjectManager;

class ReportFixtures extends Fixture implements FixtureGroupInterface
{
    public function __construct(protected ReportImporter $reportImporter)
    {
    }

    public function load(ObjectManager $manager): void
    {
        $this->reportImporter->import(__DIR__.'/example_data/reports.json');
    }

    public static function getGroups(): array
    {
        return ['imported_data'];
    }
}
