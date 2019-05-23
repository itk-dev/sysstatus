<?php

namespace App\Service;

use App\Entity\System;
use App\Repository\GroupRepository;
use App\Repository\ReportRepository;
use App\Repository\SelfServiceAvailableFromItemRepository;
use App\Repository\SystemRepository;
use Doctrine\DBAL\Types\Type;
use Doctrine\ORM\EntityManagerInterface;

class SystemImporter extends BaseImporter
{
    /** @var \App\Repository\SelfServiceAvailableFromItemRepository */
    private $selfServiceAvailableFromItemRepository;

    public function __construct(
      ReportRepository $reportRepository,
      SystemRepository $systemRepository,
      GroupRepository $groupRepository,
      SelfServiceAvailableFromItemRepository $selfServiceAvailableFromItemRepository,
      EntityManagerInterface $entityManager
    ) {
        parent::__construct( $reportRepository, $systemRepository, $groupRepository, $entityManager);

        $this->selfServiceAvailableFromItemRepository = $selfServiceAvailableFromItemRepository;
    }

    public function import($src)
    {
        $systemURL = getenv('SYSTEM_URL');

        $json = file_get_contents($src);
        $entries = json_decode($json);

        // Don't do anything if the feed is empty.
        if (0 === \count($entries)) {
            return;
        }

        // List of ids from Systemoversigten.
        $sysIds = [];

        foreach ($entries as $entry) {
            $sysIds[] = $entry->{'Id'};

            $system = $this->systemRepository->findOneBy(['sysInternalId' => $entry->{'Id'}]);

            if (!$system) {
                $system = new System();
                $system->setName($this->sanitizeText($entry->{'Titel'}));

                $this->entityManager->persist($system);
            }
            // Un-archive the system.
            $system->setArchivedAt(null);

            $system->setSysId($entry->{'Id'});
            $system->setSysInternalId($this->sanitizeText($entry->{'Id'}));

            $system->setSysUpdated($this->convertDate($entry->{'Ændret'}));
            $system->setSysTitle($this->sanitizeText($entry->{'Titel'}));

            // @TODO: Fix link
            //$system->setSysLink('');

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

            /* @TODO: Handle this.
            $system->clearSelfServiceAvailableFromItems();
            $selfServiceAvailableFromTitles = $entry->xpath('sys:link[@title="SelvbetjeningTilgængeligFra"]//sys:entry/sys:title');
            if ($selfServiceAvailableFromTitles) {
              foreach ($selfServiceAvailableFromTitles as $title) {
                $name = (string)$title;
                $item = $this->selfServiceAvailableFromItemRepository->getItem($name);
                $system->addSelfServiceAvailableFromItem($item);
              }
            }
            */

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

                if ($findGroup && is_null($system->getGroup())) {
                    $system->setGroup($findGroup);
                }

                if ($subGroupName) {
                    $system->setSysOwnerSub($subGroupName);
                }
            }
        };

        // Archive systems that no longer exist in Systemoversigten.
        $this->systemRepository->createQueryBuilder('e')
            ->update()
            ->set('e.archivedAt', ':now')
            ->setParameter('now', new \DateTime(), Type::DATETIME)
            ->where('e.sysInternalId NOT IN (:sysIds)')
            ->setParameter('sysIds', $sysIds)
            ->getQuery()
            ->execute();

        $this->entityManager->flush();
    }
}
