<?php

namespace App\Command;

use App\Repository\GroupRepository;
use App\Repository\ReportRepository;
use App\Repository\SystemRepository;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Doctrine\ORM\EntityManagerInterface;

class AssignGroupsCommand extends Command
{
    private $reportRepository;
    private $systemRepository;
    private $groupRepository;
    private $entityManager;

    public function __construct(
        ReportRepository $reportRepository,
        SystemRepository $systemRepository,
        GroupRepository $groupRepository,
        EntityManagerInterface $entityManager
    ) {
        parent::__construct();
        $this->reportRepository = $reportRepository;
        $this->systemRepository = $systemRepository;
        $this->groupRepository = $groupRepository;
        $this->entityManager = $entityManager;
    }

    protected function configure()
    {
        $this
            ->setName('itstyr:group:assign')
            ->setDescription(
                'Assign groups based on sysOwner if not already set.'
            );
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $reports = $this->reportRepository->findBy(['group' => null]);
        $systems = $this->systemRepository->findBy(['group' => null]);

        foreach ($reports as $report) {
            if (!is_null($report->getSysOwner())) {
                $e = str_replace(' ', '', $report->getSysOwner());
                $e = str_replace('–', '-', $e);
                $extract = explode('-', $e);
                $groupName = $extract[0];

                $findGroup = $this->groupRepository->findOneBy(
                    ['name' => $groupName]
                );

                if ($findGroup) {
                    $report->setGroup($findGroup);
                    $output->writeln($report->getName()." set group ".$groupName);
                }
                else {
                    $output->writeln($groupName . " not found, ignored.");
                }
            } else {
                $output->writeln(
                    $report->getName()." - ".$report->getSysOwner().' - ignored'
                );
            }
        }

        foreach ($systems as $system) {
            if (!is_null($system->getSysOwner())) {
                $e = str_replace(' ', '', $system->getSysOwner());
                $e = str_replace('–', '-', $e);
                $extract = explode('-', $e);
                $groupName = $extract[0];

                $findGroup = $this->groupRepository->findOneBy(
                    ['name' => $groupName]
                );

                if ($findGroup) {
                    $system->setGroup($findGroup);
                    $output->writeln($system->getName()." set group ".$groupName);
                }
                else {
                    $output->writeln($groupName . " not found, ignored.");
                }
            } else {
                $output->writeln(
                    $system->getName()." - ".$system->getSysOwner().' - ignored'
                );
            }
        }

        $this->entityManager->flush();
    }
}
