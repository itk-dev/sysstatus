<?php

namespace App\Service;

use App\Entity\System;
use App\Repository\GroupRepository;
use App\Repository\ReportRepository;
use App\Repository\SelfServiceAvailableFromItemRepository;
use App\Repository\SystemRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Helper\ProgressBar;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;

class SystemImporter extends BaseImporter
{
    public function __construct(
        ReportRepository $reportRepository,
        SystemRepository $systemRepository,
        GroupRepository $groupRepository,
        private readonly SelfServiceAvailableFromItemRepository $selfServiceAvailableFromItemRepository,
        EntityManagerInterface $entityManager,
        protected ParameterBagInterface $params
    ) {
        parent::__construct($reportRepository, $systemRepository, $groupRepository, $entityManager, $params);
    }

    public function import(string $src, ?ProgressBar $progressBar = null): void
    {
        $json = file_get_contents($src);
        $entries = json_decode($json);

        // Don't do anything if the feed is empty.
        if (0 === \count($entries)) {
            return;
        }

        $progressBar?->setMaxSteps(\count($entries));

        // List of ids from Systemoversigten.
        $sysInternalIds = [];

        foreach ($entries as $entry) {
            $sysInternalId = (int) $this->sanitizeText($entry->{'ID'});
            $sysInternalIds[] = $sysInternalId;

            $system = $this->systemRepository->findOneBy(['sysInternalId' => $entry->{'ID'}]);

            if (!$system) {
                $system = new System();
                $system->setName($this->sanitizeText($entry->{'Title'}));

                $this->entityManager->persist($system);
            }
            // Un-archive the system.
            $system->setArchivedAt();

            $system->setSysId($entry->{'ID'});
            $system->setSysInternalId($sysInternalId);

            $system->setSysUpdated($this->convertDate($entry->{'Modified'}));
            $system->setSysTitle($this->sanitizeText($entry->{'Title'}));

            $system->setSysLink($this->url . $entry->{'FileDirRef'}.'/DispForm.aspx?ID='.$entry->{'ID'});

            $system->setSysAlternativeTitle($this->sanitizeText($entry->{'Kaldenavn'} ?? ''));
            $system->setSysDescription($this->sanitizeText($entry->{'Beskrivelse'} ?? ''));
            $system->setSysOwner($this->sanitizeText($entry->{'Systemejerskab'} ?? ''));
            $system->setSysOwnerSubdepartment($this->sanitizeText($entry->{'Systemejerskab_x0020__x002d__x00'} ?? ''));
            $system->setSysEmergencySetup($this->sanitizeText($entry->{'Ekstern_x0020_driftsansvarlig'} ?? ''));
            $system->setSysContractor($this->sanitizeText($entry->{'Systemleverand_x00f8_r'} ?? ''));
            $system->setSysUrgencyRating($this->sanitizeText($entry->{'Urgency_x0020_rating'} ?? ''));
            $system->setSysNumberOfUsers($this->sanitizeText($entry->{'Antal_x0020_brugere'} ?? ''));
            $system->setSysTechnicalDocumentation($this->convertLink($entry->{'Teknisk_x0020_dokumentation2'} ?? null));
            $system->setSysExternalDependencies($this->sanitizeText($entry->{'Eksterne_x0020_systemafh_x00e6_n'} ?? ''));
            $system->setSysImportantInformation($this->sanitizeText($entry->{'Kommentarer'} ?? ''));
            $system->setSysEmergencySetup($this->sanitizeText($entry->{'Ekstern_x0020_driftsansvarlig'} ?? ''));
            $system->setSysSuperuserOrganization($this->convertLink($entry->{'Superbrugerorganisation'} ?? null));
            $system->setSysITSecurityCategory($this->sanitizeText($entry->{'IT_x002d_sikkerheds_x0020_katego'} ?? ''));
            $system->setSysSuperuserOrganization($this->convertLink($entry->{'Superbrugerorganisation'} ?? null));
            $system->setSysLinkToSecurityReview($this->convertLink($entry->{'Intern_x0020_IT_x002d_Sikkerheds'} ?? null));
            $system->setSysLinkToContract($this->convertLink($entry->{'Kontrakt_x0020_beskrivelse'} ?? null));
            $system->setSysEndOfContract($this->convertDate($entry->{'Kontraktudl_x00f8_bsdato'} ?? ''));
            $system->setSysOpenData($this->sanitizeText($entry->{'Open_x0020_Data'} ?? ''));
            $system->setSysOpenSource($this->sanitizeText($entry->{'Open_x0020_Source_x0020_system'} ?? ''));
            $system->setSysDigitalPost($this->sanitizeText($entry->{'Digital_x0020_post'} ?? ''));
            $system->setSysSystemCategory($this->sanitizeText(isset($entry->{'Systemkategori'}) ? $entry->{'Systemkategori'}[0] : ''));
            $system->setSysDigitalTransactionsPrYear($this->sanitizeText($entry->{'Antal_x0020_digitale_x0020_trans'} ?? ''));
            $system->setSysTotalTransactionsPrYear($this->sanitizeText($entry->{'Antal_x0020_totale_x0020_transak'} ?? ''));
            $system->setSysSelfServiceURL($this->sanitizeText($entry->{'Selvbetjenings_x002d_URL'} ?? ''));
            $system->setSysVersion($this->sanitizeText($entry->{'Versions_x0020_nummer_x002f_rele'} ?? ''));
            $system->setSysStatus($this->sanitizeText($entry->{'Arkivering'} ?? ''));
            $system->setSysSystemOwner($this->convertSystemOwner($entry->{'Systemejer2'} ?? ''));

            $selfServiceAvailableFromText = $this->convertList($entry->{'Selvbetjening_x0020_tilg_x00e6_n'} ?? null);

            if (isset($selfServiceAvailableFromText)) {
                $selfServiceAvailableFromTitles = preg_split('/;#/', $selfServiceAvailableFromText, -1, PREG_SPLIT_NO_EMPTY);

                $addToSelfServiceGroup = false;

                foreach ($selfServiceAvailableFromTitles as $title) {
                    $addToSelfServiceGroup = true;

                    $name = (string) $title;

                    $item = $this->selfServiceAvailableFromItemRepository->getItem($name);

                    $system->addSelfServiceAvailableFromItem($item);
                }

                // Add to SELVBETJENING group if the system has selvbetjening.
                if ($addToSelfServiceGroup) {
                    $findGroup = $this->groupRepository->findOneBy(
                        ['name' => 'SELVBETJENING']
                    );

                    if ($findGroup && !in_array($findGroup, $system->getGroups()->toArray())) {
                        $system->addGroup($findGroup);
                    }
                }
            }

            // Set group and subGroup.
            if (!is_null($system->getSysOwner())) {
                $e = $system->getSysOwner();
                $e = str_replace('–', '-', $e);
                $extract = explode('-', $e, 2);
                $groupName = trim($extract[0]);

                $subGroupName = trim($extract[1]);

                $findGroup = $this->groupRepository->findOneBy(
                    ['name' => $groupName]
                );

                if ($findGroup && !$system->getGroups()->contains($findGroup)) {
                    $system->addGroup($findGroup);
                }

                if ($subGroupName) {
                    $system->setSysOwnerSub($subGroupName);
                }
            }

            $progressBar?->advance();
        }

        // Archive systems that no longer exist in Systemoversigten.

        $progressBar?->setMessage('Starting archiving ...');

        $this->systemRepository->createQueryBuilder('e')
            ->update()
            ->set('e.archivedAt', ':now')
            ->setParameter('now', new \DateTime())
            ->where('e.sysInternalId NOT IN (:sysInternalIds)')
           ->setParameter('sysInternalIds', $sysInternalIds)

            ->getQuery()
            ->execute()
        ;

        $progressBar?->setMessage('Flushing ...');

        $this->entityManager->flush();

        $progressBar?->finish();
    }
}
