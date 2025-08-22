<?php

namespace App\DataFixtures;

use App\Service\SystemImporter;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Bundle\FixturesBundle\FixtureGroupInterface;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\HttpKernel\KernelInterface;

class SystemFixtures extends Fixture implements FixtureGroupInterface
{
  public function __construct(protected SystemImporter $systemImporter, protected KernelInterface $kernel)
  {
  }

  public function load(ObjectManager $manager): void
  {
    $rootPath = $this->kernel->getProjectDir();
    $this->systemImporter->import($rootPath . '/example_data/systems.json');
  }

  public static function getGroups(): array {
    return ['imported_data'];
  }
}
