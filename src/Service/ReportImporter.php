<?php

namespace App\Service;

use App\Entity\Report;
use Symfony\Component\Console\Helper\ProgressBar;

class ReportImporter extends BaseImporter
{
    /**
     * @throws \Exception
     */
    public function import(string $src, ?ProgressBar $progressBar = null): void
    {
        $json = file_get_contents($src);
        $entries = json_decode($json);

        // Don't do anything if the feed is empty.
        if (0 === \count($entries)) {
            return;
        }

        $progressBar?->setMaxSteps(\count($entries));

        // List of ids from Anmeldelsesportalen.
        $sysInternalIds = [];

        foreach ($entries as $entry) {
            $sysInternalId = (int) $this->sanitizeText($entry->{'ID'});
            $sysInternalIds[] = $sysInternalId;

            $report = $this->reportRepository->findOneBy(['sysInternalId' => $sysInternalId]);
            if (!$report) {
                $report = new Report();
                $report->setSysId($entry->{'ID'});
                $report->setName($this->sanitizeText($entry->{'Title'}));

                $this->entityManager->persist($report);
            }
            // Un-archive the report.
            $report->setArchivedAt(null);

            $report->setSysId($entry->{'ID'});
            $report->setSysInternalId($sysInternalId);
            $report->setSysUpdated($this->convertDate($entry->{'Modified'}));
            $report->setSysTitle($this->sanitizeText($entry->{'Title'}));
            $report->setSysLink($this->url.$entry->{'FileDirRef'}.'/DispForm.aspx?ID='.$entry->{'ID'});
            $report->setSysConfidentialInformation($this->convertBoolean($entry->{'Fortrolige_x0020_oplysninger'} ?? ''));
            $report->setSysAlternativeTitle($this->sanitizeText($entry->{'Title'} ?? ''));
            $report->setSysOwner($this->sanitizeText($entry->{'Systemejerskab2'} ?? ''));
            $report->setSysPurpose($this->sanitizeText($entry->{'Form_x00e5_l'} ?? ''));
            $report->setSysClassification($this->sanitizeText($entry->{'Systemets_x0020_klassifikation_x'} ?? ''));
            $report->setSysDateForRevision($this->convertDate($entry->{'Dato_x0020_for_x0020_revision'} ?? ''));
            $report->setSysPersons($this->sanitizeText($entry->{'Personkreds'} ?? ''));
            $report->setSysInformationTypes($this->sanitizeText($entry->{'Oplysningstyper'} ?? ''));
            $report->setSysDataSentTo($this->sanitizeText($entry->{'Interne_x0020_systemafh_x00e6_ng'} ?? ''));
            $report->setSysDataComeFrom($this->sanitizeText($entry->{'Eksterne_x0020_systemafh_x00e6_n'} ?? ''));
            $report->setSysDataLocation($this->sanitizeText($entry->{'Placering_x0020_af_x0020_data'} ?? ''));
            $report->setSysLatestDeletionDate($this->sanitizeText($entry->{'Hvorn_x00e5_r_x0020_slettes_x002'} ?? ''));
            $report->setSysDataProcessors($this->sanitizeText($entry->{'Databehandler'} ?? ''));
            $report->setSysDataProcessingAgreement($this->sanitizeText($entry->{'Er_x0020_der_x0020_indg_x00e5_et'} ?? ''));
            $report->setSysDataProcessingAgreementLink($this->sanitizeText($entry->{'Link_x0020_til_x0020_databehandl'} ?? ''));
            $report->setSysAuditorStatement($this->sanitizeText($entry->{'Revisorerkl_x00e6_ring'} ?? ''));
            $report->setSysAuditorStatementLink($this->sanitizeText($entry->{'Link_x0020_til_x0020_revisorerkl'} ?? ''));
            $report->setSysUsage($this->sanitizeText($entry->{'Systembrug'} ?? ''));
            $report->setSysRequestForInsight($this->sanitizeText($entry->{'Anmodning_x0020_om_x0020_indsigt'} ?? ''));
            $report->setSysDateUse($this->convertDate($entry->{'Ibrugtagning'} ?? ''));
            $report->setSysStatus($this->sanitizeText($entry->{'Status_x0020_2'} ?? ''));
            $report->setSysRemarks($this->sanitizeText($entry->{'Bem_x00e6_rkninger'} ?? ''));
            $report->setSysObligationToInform($this->sanitizeText($entry->{'Oplysningspligten'} ?? ''));
            $report->setSysLegalBasis($this->sanitizeText($entry->{'Retligt_x0020_grundlag'} ?? ''));
            $report->setSysConsent($this->sanitizeText($entry->{'Samtykke'} ?? ''));
            $report->setSysImpactAnalysis($this->sanitizeText($entry->{'Konsekvensanalyse'} ?? ''));
            $report->setSysImpactAnalysisLink($this->sanitizeText($entry->{'Link_x0020_til_x0020_konsekvensa'} ?? ''));
            $report->setSysAuthorizationProcedure($this->sanitizeText($entry->{'Autorisationsprocedure'} ?? ''));
            $report->setSysInternalInformation($this->sanitizeText($entry->{'Indsigt_x0020__x002d__x0020_inte'} ?? ''));
            $report->setSysDataWorthSaving($this->sanitizeText($entry->{'Bevaringsv_x00e6_rdige_x0020_dat'} ?? ''));
            $report->setSysDataToScience($this->sanitizeText($entry->{'Videregivelse_x0020_af_x0020_opl'} ?? ''));

            // @todo Handle array of objects
            $report->setSysSystemOwner($this->convertSystemOwner($entry->{'Systemejer2'} ?? ''));

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

                if ($findGroup && !$report->getGroups()->contains($findGroup)) {
                    $report->addGroup($findGroup);
                }

                if ($subGroupName) {
                    $report->setSysOwnerSub($subGroupName);
                }
            }

            $progressBar?->advance();
        }

        $progressBar?->setMessage('Starting archiving ...');

        // Archive reports that no longer exist in anmeldelsesportalen.
        $this->reportRepository->createQueryBuilder('e')
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
