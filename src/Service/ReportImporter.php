<?php

namespace App\Service;

use App\Entity\Report;

class ReportImporter extends BaseImporter
{
    public function import($src)
    {
        $xml = simplexml_load_file($src);

        foreach ($xml->getDocNamespaces() as $strPrefix => $strNamespace) {
            $strPrefix = "sys";
            $xml->registerXPathNamespace($strPrefix, $strNamespace);
        }

        foreach ($xml->xpath('//sys:entry') as $entry) {
            $report = $this->entityManager->getRepository('App:Report')->findOneBy(['sysId' => $entry->id]);

            if (!$report) {
                $report = new Report();
                $report->setSysId($entry->id);
                $report->setName($this->sanitizeText($entry->title));

                $this->entityManager->persist($report);
            }

            $report->setSysUpdated($this->convertDate($entry->updated));
            $report->setSysTitle($this->sanitizeText($entry->title));

            $properties = $entry->content->children('m', TRUE)->children('d', TRUE);

            $report->setSysAlternativeTitle($this->sanitizeText($properties->SystemetsKaldenavn));
            $report->setSysOwner($this->sanitizeText($properties->SystemejerskabValue));
            $report->setSysConfidentialInformation($this->convertBoolean($properties->FortroligeOplysninger));
            $report->setSysPurpose($this->sanitizeText($properties->Formål));
            $report->setSysClassification($this->sanitizeText($properties->SystemetsKlassifikationValue));
            $report->setSysDateForRevision($this->convertDate($properties->DatoForRevision));
            $report->setSysPersons($this->sanitizeText($properties->Personkreds));
            $report->setSysInformationTypes($this->sanitizeText($properties->Oplysningstyper));
            $report->setSysDatoToPreviousInternalSystemDependencies($this->sanitizeText($properties->HvorOverføresDataTilTidligereInterneSystemafhængigheder));
            $report->setSysDatoFromPreviousExternalSystemDependencies($this->sanitizeText($properties->HvorKommerDataFraTidligereEksterneSystemafhængigheder));
            $report->setSysDataLocation($this->sanitizeText($properties->PlaceringAfData));
            $report->setSysLatestDeletionDate($this->sanitizeText($properties->HvornårSlettesOplysningerneSenest));
            $report->setSysDataWorthSaving($this->convertBoolean($properties->BevaringsværdigeData));
            $report->setSysDataWorthSavingVia($this->sanitizeText($properties->BevaringsværdigeDataVia));
            $report->setSysDataProcessors($this->sanitizeText($properties->Databehandler));
            $report->setSysDataProcessingAgreement($this->sanitizeText($properties->DatabehandleraftaleValue));
            $report->setSysDataProcessingAgreementLink($this->sanitizeText($properties->LinkTilDatabehandleraftale));
            $report->setSysAuditorStatement($this->sanitizeText($properties->RevisorerklæringTilsynValue));
            $report->setSysAuditorStatementLink($this->sanitizeText($properties->LinkTilRevisorerklæring));
            $report->setSysUsage($this->sanitizeText($properties->Systembrug));
            $report->setSysRequestForInsight($this->sanitizeText($properties->AnmodningOmIndsigt));
            $report->setSysDateUse($this->convertDate($properties->Ibrugtagning));
            $report->setSysStatus($this->sanitizeText($properties->StatusValue));
            $report->setSysRemarks($this->sanitizeText($properties->Bemærkninger));
            $report->setSysVideoSuveillance($this->sanitizeText($properties->HvorTvOvervågesDerHenne));
            $report->setSysObligationToInform($this->sanitizeText($properties->Oplysningspligten));
            $report->setSysLegalBasis($this->sanitizeText($properties->RetligtGrundlag));
            $report->setSysConsent($this->sanitizeText($properties->SamtykkeValue));
            $report->setSysImpactAnalysis($this->sanitizeText($properties->KonsekvensanalyseValue));
            $report->setSysAuthorizationProcedure($this->sanitizeText($properties->Autorisationsprocedure));
            $report->setSysVersion($this->sanitizeText($properties->Version));
        };

        $this->entityManager->flush();
    }
}
