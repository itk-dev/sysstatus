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

        $xml = simplexml_load_file($src);

        foreach ($xml->getDocNamespaces() as $strPrefix => $strNamespace) {
            $strPrefix = "sys";
            $xml->registerXPathNamespace($strPrefix, $strNamespace);
        }

        $entries = $xml->xpath('/sys:feed/sys:entry');

        // Don't do anything if the feed is empty.
        if (0 === \count($entries)) {
            return;
        }

        // List of ids from Systemoversigten.
        $sysIds = [];

        foreach ($entries as $entry) {
            $entry->registerXPathNamespace('sys', 'http://www.w3.org/2005/Atom');
            $sysIds[] = (string)$entry->id;

            $system = $this->systemRepository->findOneBy(['sysId' => $entry->id]);

            if (!$system) {
                $system = new System();
                $system->setSysId($entry->id);
                $system->setName($this->sanitizeText($entry->title));

                $this->entityManager->persist($system);
            }
            // Un-archive the system.
            $system->setArchivedAt(null);

            $system->setSysUpdated($this->convertDate($entry->updated));
            $system->setSysTitle($this->sanitizeText($entry->title));

            $properties = $entry->content->children('m', TRUE)->children('d', TRUE);

            // Set link to Anmeldelsesportalen.
            $system->setSysLink($systemURL . $this->sanitizeText($properties->Sti) . '/DispForm.aspx?ID=' . $this->sanitizeText($properties->Id));

            $system->setSysInternalId($this->sanitizeText($properties->Id));
            $system->setSysAlternativeTitle($this->sanitizeText($properties->Kaldenavn));
            $system->setSysDescription($this->sanitizeText($properties->Beskrivelse));
            $system->setSysOwner($this->sanitizeText($properties->SystemejerskabValue));
            $system->setSysOwnerSubdepartment($this->sanitizeText($properties->SystemejerskabUnderafdeling));
            $system->setSysEmergencySetup($this->sanitizeText($properties->Driftsberedskab));
            $system->setSysContractor($this->sanitizeText($properties->Systemleverandør));
            $system->setSysUrgencyRating($this->sanitizeText($properties->UrgencyRatingValue));
            $system->setSysNumberOfUsers($this->sanitizeText($properties->AntalBrugereValue));
            $system->setSysTechnicalDocumentation($this->sanitizeText($properties->TekniskDokumentation));
            $system->setSysExternalDependencies($this->sanitizeText($properties->EksterneSystemafhængigheder));
            $system->setSysImportantInformation($this->sanitizeText($properties->VigtigeSupplerendeOplysninger));
            $system->setSysEmergencySetup($this->sanitizeText($properties->Driftsberedskab));
            $system->setSysSuperuserOrganization($this->sanitizeText($properties->Superbrugerorganisation));
            $system->setSysITSecurityCategory($this->sanitizeText($properties->ITSikkerhedskategoriValue));
            $system->setSysLinkToSecurityReview($this->sanitizeText($properties->LinkTilSikkerhedsanmeldelse));
            $system->setSysLinkToContract($this->sanitizeText($properties->LinkTilKontrakt));
            $system->setSysEndOfContract($this->convertDate($properties->Kontraktudløbsdato));
            $system->setSysOpenData($this->sanitizeText($properties->OpenDataValue));
            $system->setSysOpenSource($this->sanitizeText($properties->OpenSourceValue));
            $system->setSysDigitalPost($this->sanitizeText($properties->DigitalPostValue));
            $system->setSysSystemCategory($this->sanitizeText($properties->SystemkategoriValue));
            $system->setSysDigitalTransactionsPrYear($this->sanitizeText($properties->AntalDigitaleTransaktionerPrÅr));
            $system->setSysTotalTransactionsPrYear($this->sanitizeText($properties->AntalTotaleTransaktionerPrÅr));
            $system->setSysSelfServiceURL($this->sanitizeText($properties->SelvbetjeningsURL));
            $system->setSysVersion($this->sanitizeText($properties->Version));
            $system->setSysStatus($this->sanitizeText($properties->StatusValue));

            $sysSystemOwner = '';
            $content = $entry->xpath('sys:link[@title="Systemejer"]//sys:entry/sys:content');
            if (\count($content) > 0) {
              $systemOwner = $content[0]->children('m', TRUE)->children('d', TRUE);
              $sysSystemOwner = (string)$systemOwner->Navn;
            }
            $system->setSysSystemOwner($sysSystemOwner);

            $system->clearSelfServiceAvailableFromItems();
            $selfServiceAvailableFromTitles = $entry->xpath('sys:link[@title="SelvbetjeningTilgængeligFra"]//sys:entry/sys:title');
            if ($selfServiceAvailableFromTitles) {
              foreach ($selfServiceAvailableFromTitles as $title) {
                $name = (string)$title;
                $item = $this->selfServiceAvailableFromItemRepository->getItem($name);
                $system->addSelfServiceAvailableFromItem($item);
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
            ->where('e.sysId NOT IN (:sysIds)')
            ->setParameter('sysIds', $sysIds)
            ->getQuery()
            ->execute();

        $this->entityManager->flush();
    }
}
