<?php

namespace App\DataFixtures;

use App\Service\SystemImporter;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Bundle\FixturesBundle\FixtureGroupInterface;
use Doctrine\Persistence\ObjectManager;

class SystemFixtures extends Fixture implements FixtureGroupInterface
{
    public function __construct(protected SystemImporter $systemImporter)
    {
    }

    public function load(ObjectManager $manager): void
    {
        $this->systemImporter->import(__DIR__.'/example_data/systems.json');
    }

    public static function getGroups(): array
    {
        return ['imported_data'];
    }
}
