<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\SystemRepository")
 */
class System
{
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
    private $sysDescription;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private $sysOwner;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private $sysOwnerSubdepartment;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private $sysEmergencySetup;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private $sysContractor;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private $sysUrgencyRating;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private $sysNumberOfUsers;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private $sysTechnicalDocumentation;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private $sysExternalDependencies;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private $sysImportantInformation;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private $sysSuperuserOrganization;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private $sysServerNames;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private $sysITSecurityCategory;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private $sysLinkToSecurityReview;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private $sysLinkToContract;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $sysEndOfContract;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private $sysArchiving;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private $sysOpenData;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private $sysOpenSource;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private $sysDigitalPost;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private $sysSystemCategory;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private $sysDigitalTransactionsPrYear;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private $sysTotalTransactionsPrYear;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private $sysSelfServiceURL;

    public function getId()
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    public function getText(): ?string
    {
        return $this->text;
    }

    public function setText(string $text): self
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
    public function getSysDescription()
    {
        return $this->sysDescription;
    }

    /**
     * @param mixed $sysDescription
     */
    public function setSysDescription($sysDescription)
    {
        $this->sysDescription = $sysDescription;
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
    public function getSysOwnerSubdepartment()
    {
        return $this->sysOwnerSubdepartment;
    }

    /**
     * @param mixed $sysOwnerSubdepartment
     */
    public function setSysOwnerSubdepartment($sysOwnerSubdepartment)
    {
        $this->sysOwnerSubdepartment = $sysOwnerSubdepartment;
    }

    /**
     * @return mixed
     */
    public function getSysEmergencySetup()
    {
        return $this->sysEmergencySetup;
    }

    /**
     * @param mixed $sysEmergencySetup
     */
    public function setSysEmergencySetup($sysEmergencySetup)
    {
        $this->sysEmergencySetup = $sysEmergencySetup;
    }

    /**
     * @return mixed
     */
    public function getSysContractor()
    {
        return $this->sysContractor;
    }

    /**
     * @param mixed $sysContractor
     */
    public function setSysContractor($sysContractor)
    {
        $this->sysContractor = $sysContractor;
    }

    /**
     * @return mixed
     */
    public function getSysUrgencyRating()
    {
        return $this->sysUrgencyRating;
    }

    /**
     * @param mixed $sysUrgencyRating
     */
    public function setSysUrgencyRating($sysUrgencyRating)
    {
        $this->sysUrgencyRating = $sysUrgencyRating;
    }

    /**
     * @return mixed
     */
    public function getSysNumberOfUsers()
    {
        return $this->sysNumberOfUsers;
    }

    /**
     * @param mixed $sysNumberOfUsers
     */
    public function setSysNumberOfUsers($sysNumberOfUsers)
    {
        $this->sysNumberOfUsers = $sysNumberOfUsers;
    }

    /**
     * @return mixed
     */
    public function getSysTechnicalDocumentation()
    {
        return $this->sysTechnicalDocumentation;
    }

    /**
     * @param mixed $sysTechnicalDocumentation
     */
    public function setSysTechnicalDocumentation($sysTechnicalDocumentation)
    {
        $this->sysTechnicalDocumentation = $sysTechnicalDocumentation;
    }

    /**
     * @return mixed
     */
    public function getSysExternalDependencies()
    {
        return $this->sysExternalDependencies;
    }

    /**
     * @param mixed $sysExternalDependencies
     */
    public function setSysExternalDependencies($sysExternalDependencies)
    {
        $this->sysExternalDependencies = $sysExternalDependencies;
    }

    /**
     * @return mixed
     */
    public function getSysImportantInformation()
    {
        return $this->sysImportantInformation;
    }

    /**
     * @param mixed $sysImportantInformation
     */
    public function setSysImportantInformation($sysImportantInformation)
    {
        $this->sysImportantInformation = $sysImportantInformation;
    }

    /**
     * @return mixed
     */
    public function getSysSuperuserOrganization()
    {
        return $this->sysSuperuserOrganization;
    }

    /**
     * @param mixed $sysSuperuserOrganization
     */
    public function setSysSuperuserOrganization($sysSuperuserOrganization)
    {
        $this->sysSuperuserOrganization = $sysSuperuserOrganization;
    }

    /**
     * @return mixed
     */
    public function getSysServerNames()
    {
        return $this->sysServerNames;
    }

    /**
     * @param mixed $sysServerNames
     */
    public function setSysServerNames($sysServerNames)
    {
        $this->sysServerNames = $sysServerNames;
    }

    /**
     * @return mixed
     */
    public function getSysITSecurityCategory()
    {
        return $this->sysITSecurityCategory;
    }

    /**
     * @param mixed $sysITSecurityCategory
     */
    public function setSysITSecurityCategory($sysITSecurityCategory)
    {
        $this->sysITSecurityCategory = $sysITSecurityCategory;
    }

    /**
     * @return mixed
     */
    public function getSysLinkToSecurityReview()
    {
        return $this->sysLinkToSecurityReview;
    }

    /**
     * @param mixed $sysLinkToSecurityReview
     */
    public function setSysLinkToSecurityReview($sysLinkToSecurityReview)
    {
        $this->sysLinkToSecurityReview = $sysLinkToSecurityReview;
    }

    /**
     * @return mixed
     */
    public function getSysLinkToContract()
    {
        return $this->sysLinkToContract;
    }

    /**
     * @param mixed $sysLinkToContract
     */
    public function setSysLinkToContract($sysLinkToContract)
    {
        $this->sysLinkToContract = $sysLinkToContract;
    }

    /**
     * @return mixed
     */
    public function getSysEndOfContract()
    {
        return $this->sysEndOfContract;
    }

    /**
     * @param mixed $sysEndOfContract
     */
    public function setSysEndOfContract($sysEndOfContract)
    {
        $this->sysEndOfContract = $sysEndOfContract;
    }

    /**
     * @return mixed
     */
    public function getSysArchiving()
    {
        return $this->sysArchiving;
    }

    /**
     * @param mixed $sysArchiving
     */
    public function setSysArchiving($sysArchiving)
    {
        $this->sysArchiving = $sysArchiving;
    }

    /**
     * @return mixed
     */
    public function getSysOpenData()
    {
        return $this->sysOpenData;
    }

    /**
     * @param mixed $sysOpenData
     */
    public function setSysOpenData($sysOpenData)
    {
        $this->sysOpenData = $sysOpenData;
    }

    /**
     * @return mixed
     */
    public function getSysOpenSource()
    {
        return $this->sysOpenSource;
    }

    /**
     * @param mixed $sysOpenSource
     */
    public function setSysOpenSource($sysOpenSource)
    {
        $this->sysOpenSource = $sysOpenSource;
    }

    /**
     * @return mixed
     */
    public function getSysDigitalPost()
    {
        return $this->sysDigitalPost;
    }

    /**
     * @param mixed $sysDigitalPost
     */
    public function setSysDigitalPost($sysDigitalPost)
    {
        $this->sysDigitalPost = $sysDigitalPost;
    }

    /**
     * @return mixed
     */
    public function getSysSystemCategory()
    {
        return $this->sysSystemCategory;
    }

    /**
     * @param mixed $sysSystemCategory
     */
    public function setSysSystemCategory($sysSystemCategory)
    {
        $this->sysSystemCategory = $sysSystemCategory;
    }

    /**
     * @return mixed
     */
    public function getSysDigitalTransactionsPrYear()
    {
        return $this->sysDigitalTransactionsPrYear;
    }

    /**
     * @param mixed $sysDigitalTransactionsPrYear
     */
    public function setSysDigitalTransactionsPrYear(
        $sysDigitalTransactionsPrYear
    ) {
        $this->sysDigitalTransactionsPrYear = $sysDigitalTransactionsPrYear;
    }

    /**
     * @return mixed
     */
    public function getSysTotalTransactionsPrYear()
    {
        return $this->sysTotalTransactionsPrYear;
    }

    /**
     * @param mixed $sysTotalTransactionsPrYear
     */
    public function setSysTotalTransactionsPrYear($sysTotalTransactionsPrYear)
    {
        $this->sysTotalTransactionsPrYear = $sysTotalTransactionsPrYear;
    }

    /**
     * @return mixed
     */
    public function getSysSelfServiceURL()
    {
        return $this->sysSelfServiceURL;
    }

    /**
     * @param mixed $sysSelfServiceURL
     */
    public function setSysSelfServiceURL($sysSelfServiceURL)
    {
        $this->sysSelfServiceURL = $sysSelfServiceURL;
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

    public function getSysIdAsLink()
    {
        return $this->getSysId();
    }
}
