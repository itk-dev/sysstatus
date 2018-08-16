<?php

namespace App\Service;

use App\Entity\Report;

class ReportImporter extends BaseImporter
{
    public function import($src)
    {
        $systemURL = getenv('SYSTEM_URL');

        $xml = simplexml_load_file($src);

        foreach ($xml->getDocNamespaces() as $strPrefix => $strNamespace) {
            $strPrefix = "sys";
            $xml->registerXPathNamespace($strPrefix, $strNamespace);
        }

        foreach ($xml->xpath('//sys:entry') as $entry) {
            $report = $this->reportRepository->findOneBy(['sysId' => $entry->id]);

            if (!$report) {
                $report = new Report();
                $report->setSysId($entry->id);
                $report->setName($this->sanitizeText($entry->title));

                $this->entityManager->persist($report);
            }

            $report->setSysUpdated($this->convertDate($entry->updated));
            $report->setSysTitle($this->sanitizeText($entry->title));

            $properties = $entry->content->children('m', TRUE)->children('d', TRUE);

            // Set link to Anmeldelsesportalen.
            $report->setSysLink($systemURL . $this->sanitizeText($properties->Sti) . '/DispForm.aspx?ID=' . $this->sanitizeText($properties->Id));

            // Set properties 1:1
            $report->setSysInternalId($this->sanitizeText($properties->Id));
            $report->setSysConfidentialInformation($this->convertBoolean($properties->FølsommeOplysninger));
            $report->setSysAlternativeTitle($this->sanitizeText($properties->SystemetsKaldenavn));
            $report->setSysOwner($this->sanitizeText($properties->SystemejerskabValue));
            $report->setSysPurpose($this->sanitizeText($properties->Formål));
            $report->setSysClassification($this->sanitizeText($properties->SystemetsKlassifikationValue));
            $report->setSysDateForRevision($this->convertDate($properties->DatoForRevision));
            $report->setSysPersons($this->sanitizeText($properties->Personkreds));
            $report->setSysInformationTypes($this->sanitizeText($properties->Oplysningstyper));
            $report->setSysDataSentTo($this->sanitizeText($properties->HvorOverføresDataTil));
            $report->setSysDataComeFrom($this->sanitizeText($properties->HvorKommerDataFra));
            $report->setSysDataLocation($this->sanitizeText($properties->PlaceringAfData));
            $report->setSysLatestDeletionDate($this->sanitizeText($properties->HvornårSlettesOplysningerneSenest));
            $report->setSysDataProcessors($this->sanitizeText($properties->Databehandler));
            $report->setSysDataProcessingAgreement($this->sanitizeText($properties->DatabehandleraftaleFortrolighedsaftaleValue));
            $report->setSysDataProcessingAgreementLink($this->sanitizeText($properties->LinkTilDatabehandleraftaleFortrolighedsaftale));
            $report->setSysAuditorStatement($this->sanitizeText($properties->RevisorerklæringTilsynValue));
            $report->setSysAuditorStatementLink($this->sanitizeText($properties->LinkTilRevisorerklæring));
            $report->setSysUsage($this->sanitizeText($properties->Systembrug));
            $report->setSysRequestForInsight($this->sanitizeText($properties->AnmodningOmIndsigt));
            $report->setSysDateUse($this->convertDate($properties->Ibrugtagning));
            $report->setSysStatus($this->sanitizeText($properties->StatusValue));
            $report->setSysRemarks($this->sanitizeText($properties->Bemærkninger));
            $report->setSysObligationToInform($this->sanitizeText($properties->Oplysningspligten));
            $report->setSysLegalBasis($this->sanitizeText($properties->RetligtGrundlag));
            $report->setSysConsent($this->sanitizeText($properties->SamtykkeValue));
            $report->setSysImpactAnalysis($this->sanitizeText($properties->KonsekvensanalyseValue));
            $report->setSysAuthorizationProcedure($this->sanitizeText($properties->Autorisationsprocedure));
            $report->setSysVersion($this->sanitizeText($properties->Version));
            $report->setSysInternalInformation($this->sanitizeText($properties->IndsigtInterneOplysninger));
            $report->setSysDataWorthSaving($this->sanitizeText($properties->IndeholderSystemetBevaringsværdigeDataValue));
            $report->setSysDataToScience($this->sanitizeText($properties->VideregivelseAfOplysningerTilForskningValue));

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

        $this->entityManager->flush();
    }
}
