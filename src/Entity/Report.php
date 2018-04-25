<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Gedmo\Blameable\Traits\BlameableEntity;
use Gedmo\Timestampable\Traits\TimestampableEntity;

/**
 * @ORM\Entity(repositoryClass="App\Repository\ReportRepository")
 */
class Report
{
    use BlameableEntity;
    use TimestampableEntity;

    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $name;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private $text;

    /**
     * @ORM\Column(type="string", nullable=true)
     */
    private $sysId;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private $sysTitle;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private $sysAlternativeTitle;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $sysUpdated;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private $sysOwner;

    /**
     * @ORM\Column(type="boolean", nullable=true)
     */
    private $sysConfidentialInformation;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private $sysPurpose;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private $sysClassification;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $sysDateForRevision;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private $sysPersons;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private $sysInformationTypes;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private $sysDatoToPreviousInternalSystemDependencies;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private $sysDatoFromPreviousExternalSystemDependencies;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private $sysDataLocation;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private $sysLatestDeletionDate;

    /**
     * @ORM\Column(type="boolean", nullable=true)
     */
    private $sysDataWorthSaving;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private $sysDataWorthSavingVia;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private $sysDataProcessors;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private $sysDataProcessingAgreement;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private $sysDataProcessingAgreementLink;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private $sysAuditorStatement;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private $sysAuditorStatementLink;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private $sysUsage;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private $sysRequestForInsight;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $sysDateUse;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private $sysStatus;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private $sysRemarks;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private $sysVideoSuveillance;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private $sysObligationToInform;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private $sysLegalBasis;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private $sysConsent;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private $sysImpactAnalysis;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private $sysAuthorizationProcedure;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private $sysVersion;

    public function getId()
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(?string $name): self
    {
        $this->name = $name;

        return $this;
    }

    public function getText(): ?string
    {
        return $this->text;
    }

    public function setText(?string $text): self
    {
        $this->text = $text;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getSysId()
    {
        return $this->sysId;
    }

    /**
     * @param mixed $sysId
     */
    public function setSysId($sysId)
    {
        $this->sysId = $sysId;
    }

    /**
     * @return mixed
     */
    public function getSysTitle()
    {
        return $this->sysTitle;
    }

    /**
     * @param mixed $sysTitle
     */
    public function setSysTitle($sysTitle)
    {
        $this->sysTitle = $sysTitle;
    }

    /**
     * @return mixed
     */
    public function getSysAlternativeTitle()
    {
        return $this->sysAlternativeTitle;
    }

    /**
     * @param mixed $sysAlternativeTitle
     */
    public function setSysAlternativeTitle($sysAlternativeTitle)
    {
        $this->sysAlternativeTitle = $sysAlternativeTitle;
    }

    /**
     * @return mixed
     */
    public function getSysUpdated()
    {
        return $this->sysUpdated;
    }

    /**
     * @param mixed $sysUpdated
     */
    public function setSysUpdated($sysUpdated)
    {
        $this->sysUpdated = $sysUpdated;
    }

    /**
     * @return mixed
     */
    public function getSysOwner()
    {
        return $this->sysOwner;
    }

    /**
     * @param mixed $sysOwner
     */
    public function setSysOwner($sysOwner)
    {
        $this->sysOwner = $sysOwner;
    }

    /**
     * @return mixed
     */
    public function getSysConfidentialInformation()
    {
        return $this->sysConfidentialInformation;
    }

    /**
     * @param mixed $sysConfidentialInformation
     */
    public function setSysConfidentialInformation($sysConfidentialInformation)
    {
        $this->sysConfidentialInformation = $sysConfidentialInformation;
    }

    /**
     * @return mixed
     */
    public function getSysPurpose()
    {
        return $this->sysPurpose;
    }

    /**
     * @param mixed $sysPurpose
     */
    public function setSysPurpose($sysPurpose)
    {
        $this->sysPurpose = $sysPurpose;
    }

    /**
     * @return mixed
     */
    public function getSysClassification()
    {
        return $this->sysClassification;
    }

    /**
     * @param mixed $sysClassification
     */
    public function setSysClassification($sysClassification)
    {
        $this->sysClassification = $sysClassification;
    }

    /**
     * @return mixed
     */
    public function getSysDateForRevision()
    {
        return $this->sysDateForRevision;
    }

    /**
     * @param mixed $sysDateForRevision
     */
    public function setSysDateForRevision($sysDateForRevision)
    {
        $this->sysDateForRevision = $sysDateForRevision;
    }

    /**
     * @return mixed
     */
    public function getSysPersons()
    {
        return $this->sysPersons;
    }

    /**
     * @param mixed $sysPersons
     */
    public function setSysPersons($sysPersons)
    {
        $this->sysPersons = $sysPersons;
    }

    /**
     * @return mixed
     */
    public function getSysInformationTypes()
    {
        return $this->sysInformationTypes;
    }

    /**
     * @param mixed $sysInformationTypes
     */
    public function setSysInformationTypes($sysInformationTypes)
    {
        $this->sysInformationTypes = $sysInformationTypes;
    }

    /**
     * @return mixed
     */
    public function getSysDatoToPreviousInternalSystemDependencies()
    {
        return $this->sysDatoToPreviousInternalSystemDependencies;
    }

    /**
     * @param mixed $sysDatoToPreviousInternalSystemDependencies
     */
    public function setSysDatoToPreviousInternalSystemDependencies(
        $sysDatoToPreviousInternalSystemDependencies
    ) {
        $this->sysDatoToPreviousInternalSystemDependencies = $sysDatoToPreviousInternalSystemDependencies;
    }

    /**
     * @return mixed
     */
    public function getSysDatoFromPreviousExternalSystemDependencies()
    {
        return $this->sysDatoFromPreviousExternalSystemDependencies;
    }

    /**
     * @param mixed $sysDatoFromPreviousExternalSystemDependencies
     */
    public function setSysDatoFromPreviousExternalSystemDependencies(
        $sysDatoFromPreviousExternalSystemDependencies
    ) {
        $this->sysDatoFromPreviousExternalSystemDependencies = $sysDatoFromPreviousExternalSystemDependencies;
    }

    /**
     * @return mixed
     */
    public function getSysDataLocation()
    {
        return $this->sysDataLocation;
    }

    /**
     * @param mixed $sysDataLocation
     */
    public function setSysDataLocation($sysDataLocation)
    {
        $this->sysDataLocation = $sysDataLocation;
    }

    /**
     * @return mixed
     */
    public function getSysLatestDeletionDate()
    {
        return $this->sysLatestDeletionDate;
    }

    /**
     * @param mixed $sysLatestDeletionDate
     */
    public function setSysLatestDeletionDate($sysLatestDeletionDate)
    {
        $this->sysLatestDeletionDate = $sysLatestDeletionDate;
    }

    /**
     * @return mixed
     */
    public function getSysDataWorthSaving()
    {
        return $this->sysDataWorthSaving;
    }

    /**
     * @param mixed $sysDataWorthSaving
     */
    public function setSysDataWorthSaving($sysDataWorthSaving)
    {
        $this->sysDataWorthSaving = $sysDataWorthSaving;
    }

    /**
     * @return mixed
     */
    public function getSysDataWorthSavingVia()
    {
        return $this->sysDataWorthSavingVia;
    }

    /**
     * @param mixed $sysDataWorthSavingVia
     */
    public function setSysDataWorthSavingVia($sysDataWorthSavingVia)
    {
        $this->sysDataWorthSavingVia = $sysDataWorthSavingVia;
    }

    /**
     * @return mixed
     */
    public function getSysDataProcessors()
    {
        return $this->sysDataProcessors;
    }

    /**
     * @param mixed $sysDataProcessors
     */
    public function setSysDataProcessors($sysDataProcessors)
    {
        $this->sysDataProcessors = $sysDataProcessors;
    }

    /**
     * @return mixed
     */
    public function getSysDataProcessingAgreement()
    {
        return $this->sysDataProcessingAgreement;
    }

    /**
     * @param mixed $sysDataProcessingAgreement
     */
    public function setSysDataProcessingAgreement($sysDataProcessingAgreement)
    {
        $this->sysDataProcessingAgreement = $sysDataProcessingAgreement;
    }

    /**
     * @return mixed
     */
    public function getSysDataProcessingAgreementLink()
    {
        return $this->sysDataProcessingAgreementLink;
    }

    /**
     * @param mixed $sysDataProcessingAgreementLink
     */
    public function setSysDataProcessingAgreementLink(
        $sysDataProcessingAgreementLink
    ) {
        $this->sysDataProcessingAgreementLink = $sysDataProcessingAgreementLink;
    }

    /**
     * @return mixed
     */
    public function getSysAuditorStatement()
    {
        return $this->sysAuditorStatement;
    }

    /**
     * @param mixed $sysAuditorStatement
     */
    public function setSysAuditorStatement($sysAuditorStatement)
    {
        $this->sysAuditorStatement = $sysAuditorStatement;
    }

    /**
     * @return mixed
     */
    public function getSysAuditorStatementLink()
    {
        return $this->sysAuditorStatementLink;
    }

    /**
     * @param mixed $sysAuditorStatementLink
     */
    public function setSysAuditorStatementLink($sysAuditorStatementLink)
    {
        $this->sysAuditorStatementLink = $sysAuditorStatementLink;
    }

    /**
     * @return mixed
     */
    public function getSysUsage()
    {
        return $this->sysUsage;
    }

    /**
     * @param mixed $sysUsage
     */
    public function setSysUsage($sysUsage)
    {
        $this->sysUsage = $sysUsage;
    }

    /**
     * @return mixed
     */
    public function getSysRequestForInsight()
    {
        return $this->sysRequestForInsight;
    }

    /**
     * @param mixed $sysRequestForInsight
     */
    public function setSysRequestForInsight($sysRequestForInsight)
    {
        $this->sysRequestForInsight = $sysRequestForInsight;
    }

    /**
     * @return mixed
     */
    public function getSysDateUse()
    {
        return $this->sysDateUse;
    }

    /**
     * @param mixed $sysDateUse
     */
    public function setSysDateUse($sysDateUse)
    {
        $this->sysDateUse = $sysDateUse;
    }

    /**
     * @return mixed
     */
    public function getSysStatus()
    {
        return $this->sysStatus;
    }

    /**
     * @param mixed $sysStatus
     */
    public function setSysStatus($sysStatus)
    {
        $this->sysStatus = $sysStatus;
    }

    /**
     * @return mixed
     */
    public function getSysRemarks()
    {
        return $this->sysRemarks;
    }

    /**
     * @param mixed $sysRemarks
     */
    public function setSysRemarks($sysRemarks)
    {
        $this->sysRemarks = $sysRemarks;
    }

    /**
     * @return mixed
     */
    public function getSysVideoSuveillance()
    {
        return $this->sysVideoSuveillance;
    }

    /**
     * @param mixed $sysVideoSuveillance
     */
    public function setSysVideoSuveillance($sysVideoSuveillance)
    {
        $this->sysVideoSuveillance = $sysVideoSuveillance;
    }

    /**
     * @return mixed
     */
    public function getSysObligationToInform()
    {
        return $this->sysObligationToInform;
    }

    /**
     * @param mixed $sysObligationToInform
     */
    public function setSysObligationToInform($sysObligationToInform)
    {
        $this->sysObligationToInform = $sysObligationToInform;
    }

    /**
     * @return mixed
     */
    public function getSysLegalBasis()
    {
        return $this->sysLegalBasis;
    }

    /**
     * @param mixed $sysLegalBasis
     */
    public function setSysLegalBasis($sysLegalBasis)
    {
        $this->sysLegalBasis = $sysLegalBasis;
    }

    /**
     * @return mixed
     */
    public function getSysConsent()
    {
        return $this->sysConsent;
    }

    /**
     * @param mixed $sysConsent
     */
    public function setSysConsent($sysConsent)
    {
        $this->sysConsent = $sysConsent;
    }

    /**
     * @return mixed
     */
    public function getSysImpactAnalysis()
    {
        return $this->sysImpactAnalysis;
    }

    /**
     * @param mixed $sysImpactAnalysis
     */
    public function setSysImpactAnalysis($sysImpactAnalysis)
    {
        $this->sysImpactAnalysis = $sysImpactAnalysis;
    }

    /**
     * @return mixed
     */
    public function getSysAuthorizationProcedure()
    {
        return $this->sysAuthorizationProcedure;
    }

    /**
     * @param mixed $sysAuthorizationProcedure
     */
    public function setSysAuthorizationProcedure($sysAuthorizationProcedure)
    {
        $this->sysAuthorizationProcedure = $sysAuthorizationProcedure;
    }

    /**
     * @return mixed
     */
    public function getSysVersion()
    {
        return $this->sysVersion;
    }

    /**
     * @param mixed $sysVersion
     */
    public function setSysVersion($sysVersion)
    {
        $this->sysVersion = $sysVersion;
    }

}
