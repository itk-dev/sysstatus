<?php

namespace App\Service;

use App\Entity\Report;
use Doctrine\DBAL\Types\Type;

class ReportImporter extends BaseImporter
{
    public function import($src)
    {
        $systemURL = getenv('SYSTEM_URL');

        $json = file_get_contents($src);
        $entries = json_decode($json);

        // Don't do anything if the feed is empty.
        if (0 === \count($entries)) {
            return;
        }

        // List of ids from Anmeldelsesportalen.
        $sysIds = [];

        foreach ($entries as $entry) {
            $sysIds[] = $entry->{'Id'};

            $report = $this->reportRepository->findOneBy(['sysInternalId' => $entry->{'Id'}]);
            if (!$report) {
                $report = new Report();
                $report->setSysId($entry->{'Id'});
                $report->setName($this->sanitizeText($entry->{'Titel'}));

                $this->entityManager->persist($report);
            }
            // Un-archive the report.
            $report->setArchivedAt(null);

            $report->setSysId($entry->{'Id'});
            $report->setSysInternalId($this->sanitizeText($entry->{'Id'}));

            $report->setSysUpdated($this->convertDate($entry->{'Ændret'}));
            $report->setSysTitle($this->sanitizeText($entry->{'Titel'}));

            $report->setSysLink($systemURL . '/' .  $entry->{'Sti'} . '/DispForm.aspx?ID=' . $entry->{'Id'});

            $report->setSysConfidentialInformation($this->convertBoolean($entry->{'Følsomme oplysninger'}));
            $report->setSysAlternativeTitle($this->sanitizeText($entry->{'Systemnavn'}));
            $report->setSysOwner($this->sanitizeText($entry->{'Systemejerskab'}));
            $report->setSysPurpose($this->sanitizeText($entry->{'Formål'}));
            $report->setSysClassification($this->sanitizeText($entry->{'Systemets klassifikation'}));
            $report->setSysDateForRevision($this->convertDate($entry->{'Dato for revision'}));
            $report->setSysPersons($this->sanitizeText($entry->{'Personkreds'}));
            $report->setSysInformationTypes($this->sanitizeText($entry->{'Oplysningstyper'}));
            $report->setSysDataSentTo($this->sanitizeText($entry->{'Hvor overføres data til?'}));
            $report->setSysDataComeFrom($this->sanitizeText($entry->{'Hvor kommer data fra?'}));
            $report->setSysDataLocation($this->sanitizeText($entry->{'Placering af data'}));
            $report->setSysLatestDeletionDate($this->sanitizeText($entry->{'Hvornår slettes oplysningerne senest'}));
            $report->setSysDataProcessors($this->sanitizeText($entry->{'Databehandler'}));
            $report->setSysDataProcessingAgreement($this->sanitizeText($entry->{'Databehandleraftale/fortrolighedsaftale'}));
            $report->setSysDataProcessingAgreementLink($this->sanitizeText($entry->{'Link til databehandleraftale/fortrolighedsaftale'}));
            $report->setSysAuditorStatement($this->sanitizeText($entry->{'Revisorerklæring/tilsyn'}));
            $report->setSysAuditorStatementLink($this->sanitizeText($entry->{'Link til revisorerklæring'}));
            $report->setSysUsage($this->sanitizeText($entry->{'Systembrug'}));
            $report->setSysRequestForInsight($this->sanitizeText($entry->{'Anmodning om indsigt'}));
            $report->setSysDateUse($this->convertDate($entry->{'Ibrugtagning'}));
            $report->setSysStatus($this->sanitizeText($entry->{'Status'}));
            $report->setSysRemarks($this->sanitizeText($entry->{'Bemærkninger'}));
            $report->setSysObligationToInform($this->sanitizeText($entry->{'Oplysningspligten'}));
            $report->setSysLegalBasis($this->sanitizeText($entry->{'Retligt grundlag'}));
            $report->setSysConsent($this->sanitizeText($entry->{'Samtykke'}));
            $report->setSysImpactAnalysis($this->sanitizeText($entry->{'Konsekvensanalyse'}));
            $report->setSysImpactAnalysisLink($this->sanitizeText($entry->{'Link til konsekvensanalyse'}));
            $report->setSysAuthorizationProcedure($this->sanitizeText($entry->{'Autorisationsprocedure'}));
            $report->setSysInternalInformation($this->sanitizeText($entry->{'Indsigt - interne oplysninger'}));
            $report->setSysDataWorthSaving($this->sanitizeText($entry->{'Indeholder systemet bevaringsværdige data?'}));
            $report->setSysDataToScience($this->sanitizeText($entry->{'Videregivelse af oplysninger til forskning'}));

            $report->setSysSystemOwner($this->sanitizeText($entry->{'Systemejer/projektejer'}));

            // Set group and subGroup.
            if (!is_null($report->getSysOwner())) {
                $e = $report->getSysOwner();
                $e = str_replace('–', '-', $e);
                $extract = explode('-', $e, 2);
                $groupName = trim($extract[0]);

                $subGroupName = trim($extract[1]);

                $findGroup = $this->groupRepository->findOneBy(
                    ['name' => $groupName]
                );

                if ($findGroup && is_null($report->getGroup())) {
                    $report->setGroup($findGroup);
                }

                if ($subGroupName) {
                    $report->setSysOwnerSub($subGroupName);
                }
            }
        }

        // Archive reports that no longer exist in anmeldelsesportalen.
        $this->reportRepository->createQueryBuilder('e')
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
