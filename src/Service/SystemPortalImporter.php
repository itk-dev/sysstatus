<?php

namespace App\Service;

use App\Entity\System;
use Doctrine\ORM\EntityManagerInterface;

class SystemPortalImporter
{
    private $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    private function sanitizeText(string $str) {
        $newStr = html_entity_decode($str);
        $newStr = strip_tags($newStr, '<p>');
        $newStr = str_replace('<p>', '', $newStr);
        $newStr = str_replace('</p>', "\n", $newStr);
        return $newStr;
    }

    private function convertDate(string $date) {
        if (!is_string($date)) {
            return null;
        }

        $new = new \DateTime($date);

        return $new;
    }

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

                $this->entityManager->persist($system);
            }

            $system->setName($this->sanitizeText($entry->title));

            $system->setSysUpdated($this->convertDate($entry->updated));

            $properties = $entry->content->children('m', TRUE)->children('d', TRUE);

            $system->setSysTitle($this->sanitizeText($entry->title));

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
        };

        $this->entityManager->flush();
    }
}
