<?php

namespace App\Service;

use App\Entity\System;
use App\Repository\GroupRepository;
use App\Repository\ReportRepository;
use App\Repository\SelfServiceAvailableFromItemRepository;
use App\Repository\SystemRepository;
use Doctrine\ORM\EntityManagerInterface;

class SystemImporter extends BaseImporter
{
    private SelfServiceAvailableFromItemRepository $selfServiceAvailableFromItemRepository;

    public function __construct(
        ReportRepository $reportRepository,
        SystemRepository $systemRepository,
        GroupRepository $groupRepository,
        SelfServiceAvailableFromItemRepository $selfServiceAvailableFromItemRepository,
        EntityManagerInterface $entityManager,
    ) {
        parent::__construct($reportRepository, $systemRepository, $groupRepository, $entityManager);

        $this->selfServiceAvailableFromItemRepository = $selfServiceAvailableFromItemRepository;
    }

    public function import(string $src)
    {
        $systemURL = getenv('SYSTEM_URL');

        $json = file_get_contents($src);
        $entries = json_decode($json);

        // Don't do anything if the feed is empty.
        if (0 === \count($entries)) {
            return;
        }

        // List of ids from Systemoversigten.
        $sysInternalIds = [];

        foreach ($entries as $entry) {
            $sysInternalId = $this->sanitizeText($entry->{'Id'});
            $sysInternalIds[] = $sysInternalId;

            $system = $this->systemRepository->findOneBy(['sysInternalId' => $entry->{'Id'}]);

            if (!$system) {
                $system = new System();
                $system->setName($this->sanitizeText($entry->{'Titel'}));

                $this->entityManager->persist($system);
            }
            // Un-archive the system.
            $system->setArchivedAt(null);

            $system->setSysId($entry->{'Id'});
            $system->setSysInternalId($sysInternalId);

            $system->setSysUpdated($this->convertDate($entry->{'Ændret'}));
            $system->setSysTitle($this->sanitizeText($entry->{'Titel'}));

            $system->setSysLink($systemURL.'/'.$entry->{'Sti'}.'/DispForm.aspx?ID='.$entry->{'Id'});

            $system->setSysAlternativeTitle($this->sanitizeText($entry->{'Kaldenavn'}));
            $system->setSysDescription($this->sanitizeText($entry->{'Beskrivelse'}));
            $system->setSysOwner($this->sanitizeText($entry->{'Systemejerskab'}));
            $system->setSysOwnerSubdepartment($this->sanitizeText($entry->{'Systemejerskab - underafdeling'}));
            $system->setSysEmergencySetup($this->sanitizeText($entry->{'Driftsberedskab'}));
            $system->setSysContractor($this->sanitizeText($entry->{'Systemleverandør'}));
            $system->setSysUrgencyRating($this->sanitizeText($entry->{'Urgency rating'}));
            $system->setSysNumberOfUsers($this->sanitizeText($entry->{'Antal brugere'}));
            $system->setSysTechnicalDocumentation($this->sanitizeText($entry->{'Teknisk dokumentation'}));
            $system->setSysExternalDependencies($this->sanitizeText($entry->{'Eksterne systemafhængigheder'}));
            $system->setSysImportantInformation($this->sanitizeText($entry->{'Vigtige supplerende oplysninger'}));
            $system->setSysEmergencySetup($this->sanitizeText($entry->{'Driftsberedskab'}));
            $system->setSysSuperuserOrganization($this->sanitizeText($entry->{'Superbrugerorganisation'}));
            $system->setSysITSecurityCategory($this->sanitizeText($entry->{'IT-sikkerhedskategori'}));
            $system->setSysLinkToSecurityReview($this->sanitizeText($entry->{'Link til sikkerhedsanmeldelse'}));
            $system->setSysLinkToContract($this->sanitizeText($entry->{'Link til kontrakt'}));
            $system->setSysEndOfContract($this->convertDate($entry->{'Kontraktudløbsdato'}));
            $system->setSysOpenData($this->sanitizeText($entry->{'Open Data'}));
            $system->setSysOpenSource($this->sanitizeText($entry->{'Open Source'}));
            $system->setSysDigitalPost($this->sanitizeText($entry->{'Digital post'}));
            $system->setSysSystemCategory($this->sanitizeText($entry->{'Systemkategori'}));
            $system->setSysDigitalTransactionsPrYear($this->sanitizeText($entry->{'Antal digitale transaktioner pr. år'}));
            $system->setSysTotalTransactionsPrYear($this->sanitizeText($entry->{'Antal totale transaktioner pr. år'}));
            $system->setSysSelfServiceURL($this->sanitizeText($entry->{'Selvbetjenings-URL'}));
            $system->setSysVersion($this->sanitizeText($entry->{'Versions nummer/release nummer'}));
            $system->setSysStatus($this->sanitizeText($entry->{'Status'}));
            $system->setSysSystemOwner($this->sanitizeText($entry->{'Systemejer'}));

            $selfServiceAvailableFromText = $this->sanitizeText($entry->{'Selvbetjening tilgængelig fra'});

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
        }

        // Archive systems that no longer exist in Systemoversigten.

        $this->systemRepository->createQueryBuilder('e')
            ->update()
            ->set('e.archivedAt', ':now')
            ->setParameter('now', new \DateTime())
            ->where('e.sysInternalId NOT IN (:sysInternalIds)')
           ->setParameter('sysInternalIds', $sysInternalIds)

            ->getQuery()
            ->execute()
        ;

        $this->entityManager->flush();
    }
}
