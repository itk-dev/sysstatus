<?php

namespace App\Service;

use App\Entity\System;

class SystemImporter extends BaseImporter
{
    public function import($src)
    {
        $xml = simplexml_load_file($src);

        foreach ($xml->getDocNamespaces() as $strPrefix => $strNamespace) {
            $strPrefix = "sys";
            $xml->registerXPathNamespace($strPrefix, $strNamespace);
        }

        foreach ($xml->xpath('//sys:entry') as $entry) {
            $system = $this->entityManager->getRepository('App:System')->findOneBy(['sysId' => $entry->id]);

            if (!$system) {
                $system = new System();
                $system->setSysId($entry->id);
                $system->setName($this->sanitizeText($entry->title));

                $this->entityManager->persist($system);
            }

            $system->setSysUpdated($this->convertDate($entry->updated));
            $system->setSysTitle($this->sanitizeText($entry->title));

            $properties = $entry->content->children('m', TRUE)->children('d', TRUE);

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
            $system->setSysArchiving($this->sanitizeText($properties->ArkiveringValue));
            $system->setSysOpenData($this->sanitizeText($properties->OpenDataValue));
            $system->setSysOpenSource($this->sanitizeText($properties->OpenSourceValue));
            $system->setSysDigitalPost($this->sanitizeText($properties->DigitalPostValue));
            $system->setSysSystemCategory($this->sanitizeText($properties->SystemkategoriValue));
            $system->setSysDigitalTransactionsPrYear($this->sanitizeText($properties->AntalDigitaleTransaktionerPrÅr));
            $system->setSysTotalTransactionsPrYear($this->sanitizeText($properties->AntalTotaleTransaktionerPrÅr));
            $system->setSysSelfServiceURL($this->sanitizeText($properties->SelvbetjeningsURL));
            $system->setSysVersion($this->sanitizeText($properties->Version));
        };

        $this->entityManager->flush();
    }
}
