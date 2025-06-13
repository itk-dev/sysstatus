<?php

namespace App\Command;

use App\Entity\Report;
use App\Repository\GroupRepository;
use App\Repository\ReportRepository;
use App\Repository\SystemRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class AssignGroupsCommand extends Command
{
    public function __construct(
        private readonly ReportRepository $reportRepository,
        private readonly SystemRepository $systemRepository,
        private readonly GroupRepository $groupRepository,
        private readonly EntityManagerInterface $entityManager,
    ) {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this
            ->setName('itstyr:group:assign')
            ->setDescription(
                'Assign groups based on sysOwner if not already set.'
            )
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $reports = $this->reportRepository->findAll();
        $systems = $this->systemRepository->findAll();

        /** @var Report $report */
        foreach ($reports as $report) {
            if (!is_null($report->getSysOwner())) {
                $e = $report->getSysOwner();
                $e = str_replace('–', '-', $e);
                $extract = explode('-', $e, 2);
                $groupName = trim($extract[0]);

                $subGroupName = trim($extract[1]);

                $findGroup = $this->groupRepository->findOneBy(
                    ['name' => $groupName]
                );

                if ($findGroup) {
                    if (is_null($report->getGroups())) {
                        $report->addGroup($findGroup);
                    }

                    if (is_null($report->getSysOwnerSub())) {
                        $report->setSysOwnerSub($subGroupName);
                    }

                    $output->writeln('"'.$report->getName().'" set group "'.$groupName.'" and subGroup: "'.$subGroupName.'"');
                } else {
                    $output->writeln($groupName.' not found, ignored.');
                }
            } else {
                $output->writeln(
                    $report->getName().' - '.$report->getSysOwner().' - ignored'
                );
            }
        }

        foreach ($systems as $system) {
            if (!is_null($system->getSysOwner())) {
                $e = $system->getSysOwner();
                $e = str_replace('–', '-', $e);
                $extract = explode('-', $e, 2);
                $groupName = trim($extract[0]);

                $subGroupName = trim($extract[1]);

                $findGroup = $this->groupRepository->findOneBy(
                    ['name' => $groupName]
                );

                if ($findGroup) {
                    if (is_null($system->getGroups())) {
                        $system->addGroup($findGroup);
                    }

                    if (is_null($system->getSysOwnerSub())) {
                        $system->setSysOwnerSub($subGroupName);
                    }

                    $output->writeln('"'.$system->getName().'" set group "'.$groupName.'" and subGroup: "'.$subGroupName.'"');
                } else {
                    $output->writeln($groupName.' not found, ignored.');
                }
            } else {
                $output->writeln(
                    $system->getName().' - '.$system->getSysOwner().' - ignored'
                );
            }
        }

        $this->entityManager->flush();

        return Command::SUCCESS;
    }
}
