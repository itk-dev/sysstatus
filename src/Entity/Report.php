<?php

namespace App\Entity;

use App\Repository\ReportRepository;
use App\Traits\ArchivableEntity;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Blameable\Traits\BlameableEntity;
use Gedmo\Mapping\Annotation\Loggable;
use Gedmo\Mapping\Annotation\Versioned;
use Gedmo\Timestampable\Traits\TimestampableEntity;

#[ORM\Entity(repositoryClass: ReportRepository::class)]
#[Loggable]
class Report
{
    use BlameableEntity;
    use TimestampableEntity;
    use ArchivableEntity;

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(nullable: true)]
    private ?int $id = null;

    /**
     * @var Collection<int, Answer>
     */
    #[ORM\OneToMany(mappedBy: 'report', targetEntity: Answer::class)]
    private Collection $answers;

    #[ORM\Column(length: 255, nullable: true)]
    #[Versioned]
    private ?string $name = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    #[Versioned]
    private ?string $text = null;

    #[ORM\Column(length: 255, nullable: true)]
    #[Versioned]
    private ?string $sysSystemOwner = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $sysId = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $sysTitle = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $sysAlternativeTitle = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE, nullable: true)]
    private ?\DateTimeInterface $sysUpdated = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $sysOwner = null;

    #[ORM\Column(nullable: true)]
    private ?bool $sysConfidentialInformation = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $sysPurpose = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $sysClassification = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE, nullable: true)]
    private ?\DateTimeInterface $sysDateForRevision = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $sysPersons = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $sysInformationTypes = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $sysDataLocation = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $sysLatestDeletionDate = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $sysDataProcessors = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $sysDataProcessingAgreement = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $sysDataProcessingAgreementLink = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $sysAuditorStatement = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $sysAuditorStatementLink = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $sysUsage = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $sysRequestForInsight = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE, nullable: true)]
    private ?\DateTimeInterface $sysDateUse = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $sysStatus = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $sysRemarks = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $sysObligationToInform = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $sysLegalBasis = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $sysConsent = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $sysImpactAnalysis = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $sysAuthorizationProcedure = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $sysVersion = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $sysOwnerSub = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $sysInternalInformation = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $sysLink = null;

    #[ORM\Column(nullable: true)]
    private ?int $sysInternalId = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $sysDataSentTo = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $sysDataComeFrom = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $sysDataWorthSaving = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $sysDataToScience = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $sysImpactAnalysisLink = null;

    #[ORM\Column(name: 'edoc_url', length: 255, nullable: true)]
    private ?string $eDocUrl = null;

    /**
     * @var Collection<int, Group>
     */
    #[ORM\ManyToMany(targetEntity: Group::class, inversedBy: 'reports')]
    private Collection $groups;

    public function __construct()
    {
        $this->answers = new ArrayCollection();
        $this->groups = new ArrayCollection();
    }

    public function __toString()
    {
        return $this->getName();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * @return Collection<int, Answer>
     */
    public function getAnswers(): Collection
    {
        return $this->answers;
    }

    public function addAnswer(Answer $answer): self
    {
        if (!$this->answers->contains($answer)) {
            $this->answers->add($answer);
            $answer->setReport($this);
        }

        return $this;
    }

    public function removeAnswer(Answer $answer): self
    {
        if ($this->answers->removeElement($answer)) {
            // set the owning side to null (unless already changed)
            if ($answer->getReport() === $this) {
                $answer->setReport(null);
            }
        }

        return $this;
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

    /**
     * Virtual property.
     */
    public function getTextSet(): bool
    {
        return isset($this->text);
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

    public function getSysSystemOwner(): ?string
    {
        return $this->sysSystemOwner;
    }

    public function setSysSystemOwner(string $sysSystemOwner): self
    {
        $this->sysSystemOwner = $sysSystemOwner;

        return $this;
    }

    public function getSysId(): ?string
    {
        return $this->sysId;
    }

    public function setSysId(?string $sysId): self
    {
        $this->sysId = $sysId;

        return $this;
    }

    public function getSysTitle(): ?string
    {
        return $this->sysTitle;
    }

    public function setSysTitle(?string $sysTitle): self
    {
        $this->sysTitle = $sysTitle;

        return $this;
    }

    public function getSysAlternativeTitle(): ?string
    {
        return $this->sysAlternativeTitle;
    }

    public function setSysAlternativeTitle(?string $sysAlternativeTitle): self
    {
        $this->sysAlternativeTitle = $sysAlternativeTitle;

        return $this;
    }

    public function getSysUpdated(): ?\DateTimeInterface
    {
        return $this->sysUpdated;
    }

    public function setSysUpdated(?\DateTimeInterface $sysUpdated): self
    {
        $this->sysUpdated = $sysUpdated;

        return $this;
    }

    public function getSysOwner(): ?string
    {
        return $this->sysOwner;
    }

    public function setSysOwner(?string $sysOwner): self
    {
        $this->sysOwner = $sysOwner;

        return $this;
    }

    public function isSysConfidentialInformation(): ?bool
    {
        return $this->sysConfidentialInformation;
    }

    public function setSysConfidentialInformation(?bool $sysConfidentialInformation): self
    {
        $this->sysConfidentialInformation = $sysConfidentialInformation;

        return $this;
    }

    public function getSysPurpose(): ?string
    {
        return $this->sysPurpose;
    }

    public function setSysPurpose(?string $sysPurpose): self
    {
        $this->sysPurpose = $sysPurpose;

        return $this;
    }

    public function getSysClassification(): ?string
    {
        return $this->sysClassification;
    }

    public function setSysClassification(?string $sysClassification): self
    {
        $this->sysClassification = $sysClassification;

        return $this;
    }

    public function getSysDateForRevision(): ?\DateTimeInterface
    {
        return $this->sysDateForRevision;
    }

    public function setSysDateForRevision(?\DateTimeInterface $sysDateForRevision): self
    {
        $this->sysDateForRevision = $sysDateForRevision;

        return $this;
    }

    public function getSysPersons(): ?string
    {
        return $this->sysPersons;
    }

    public function setSysPersons(?string $sysPersons): self
    {
        $this->sysPersons = $sysPersons;

        return $this;
    }

    public function getSysInformationTypes(): ?string
    {
        return $this->sysInformationTypes;
    }

    public function setSysInformationTypes(?string $sysInformationTypes): self
    {
        $this->sysInformationTypes = $sysInformationTypes;

        return $this;
    }

    public function getSysDataLocation(): ?string
    {
        return $this->sysDataLocation;
    }

    public function setSysDataLocation(?string $sysDataLocation): self
    {
        $this->sysDataLocation = $sysDataLocation;

        return $this;
    }

    public function getSysLatestDeletionDate(): ?string
    {
        return $this->sysLatestDeletionDate;
    }

    public function setSysLatestDeletionDate(?string $sysLatestDeletionDate): self
    {
        $this->sysLatestDeletionDate = $sysLatestDeletionDate;

        return $this;
    }

    public function getSysDataProcessors(): ?string
    {
        return $this->sysDataProcessors;
    }

    public function setSysDataProcessors(?string $sysDataProcessors): self
    {
        $this->sysDataProcessors = $sysDataProcessors;

        return $this;
    }

    public function getSysDataProcessingAgreement(): ?string
    {
        return $this->sysDataProcessingAgreement;
    }

    public function setSysDataProcessingAgreement(?string $sysDataProcessingAgreement): self
    {
        $this->sysDataProcessingAgreement = $sysDataProcessingAgreement;

        return $this;
    }

    public function getSysDataProcessingAgreementLink(): ?string
    {
        return $this->sysDataProcessingAgreementLink;
    }

    public function setSysDataProcessingAgreementLink(?string $sysDataProcessingAgreementLink): self
    {
        $this->sysDataProcessingAgreementLink = $sysDataProcessingAgreementLink;

        return $this;
    }

    public function getSysAuditorStatement(): ?string
    {
        return $this->sysAuditorStatement;
    }

    public function setSysAuditorStatement(?string $sysAuditorStatement): self
    {
        $this->sysAuditorStatement = $sysAuditorStatement;

        return $this;
    }

    public function getSysAuditorStatementLink(): ?string
    {
        return $this->sysAuditorStatementLink;
    }

    public function setSysAuditorStatementLink(?string $sysAuditorStatementLink): self
    {
        $this->sysAuditorStatementLink = $sysAuditorStatementLink;

        return $this;
    }

    public function getSysUsage(): ?string
    {
        return $this->sysUsage;
    }

    public function setSysUsage(string $sysUsage): self
    {
        $this->sysUsage = $sysUsage;

        return $this;
    }

    public function getSysRequestForInsight(): ?string
    {
        return $this->sysRequestForInsight;
    }

    public function setSysRequestForInsight(?string $sysRequestForInsight): self
    {
        $this->sysRequestForInsight = $sysRequestForInsight;

        return $this;
    }

    public function getSysDateUse(): ?\DateTimeInterface
    {
        return $this->sysDateUse;
    }

    public function setSysDateUse(?\DateTimeInterface $sysDateUse): self
    {
        $this->sysDateUse = $sysDateUse;

        return $this;
    }

    public function getSysStatus(): ?string
    {
        return $this->sysStatus;
    }

    public function setSysStatus(?string $sysStatus): self
    {
        $this->sysStatus = $sysStatus;

        return $this;
    }

    public function getSysRemarks(): ?string
    {
        return $this->sysRemarks;
    }

    public function setSysRemarks(?string $sysRemarks): self
    {
        $this->sysRemarks = $sysRemarks;

        return $this;
    }

    public function getSysObligationToInform(): ?string
    {
        return $this->sysObligationToInform;
    }

    public function setSysObligationToInform(?string $sysObligationToInform): self
    {
        $this->sysObligationToInform = $sysObligationToInform;

        return $this;
    }

    public function getSysLegalBasis(): ?string
    {
        return $this->sysLegalBasis;
    }

    public function setSysLegalBasis(?string $sysLegalBasis): self
    {
        $this->sysLegalBasis = $sysLegalBasis;

        return $this;
    }

    public function getSysConsent(): ?string
    {
        return $this->sysConsent;
    }

    public function setSysConsent(?string $sysConsent): self
    {
        $this->sysConsent = $sysConsent;

        return $this;
    }

    public function getSysImpactAnalysis(): ?string
    {
        return $this->sysImpactAnalysis;
    }

    public function setSysImpactAnalysis(?string $sysImpactAnalysis): self
    {
        $this->sysImpactAnalysis = $sysImpactAnalysis;

        return $this;
    }

    public function getSysAuthorizationProcedure(): ?string
    {
        return $this->sysAuthorizationProcedure;
    }

    public function setSysAuthorizationProcedure(?string $sysAuthorizationProcedure): self
    {
        $this->sysAuthorizationProcedure = $sysAuthorizationProcedure;

        return $this;
    }

    public function getSysVersion(): ?string
    {
        return $this->sysVersion;
    }

    public function setSysVersion(?string $sysVersion): self
    {
        $this->sysVersion = $sysVersion;

        return $this;
    }

    /**
     * Virtual property.
     */
    public function getShowableName(): ?string
    {
        return $this->sysTitle ?? $this->getName();
    }

    public function getSysOwnerSub(): ?string
    {
        return $this->sysOwnerSub;
    }

    public function setSysOwnerSub(?string $sysOwnerSub): self
    {
        $this->sysOwnerSub = $sysOwnerSub;

        return $this;
    }

    /**
     * Virtual property.
     *
     * @return array<Theme>
     */
    public function getAnswerArea(): array
    {
        $themes = [];
        $groups = $this->getGroups();

        foreach ($groups as $group) {
            $themes = array_merge($themes, $group->getReportThemes()->toArray());
        }

        return $themes;
    }

    public function getSysInternalInformation(): ?string
    {
        return $this->sysInternalInformation;
    }

    public function setSysInternalInformation(?string $sysInternalInformation): self
    {
        $this->sysInternalInformation = $sysInternalInformation;

        return $this;
    }

    public function getSysLink(): ?string
    {
        return $this->sysLink;
    }

    public function setSysLink(string $sysLink): self
    {
        $this->sysLink = $sysLink;

        return $this;
    }

    public function getSysInternalId(): ?int
    {
        return $this->sysInternalId;
    }

    public function setSysInternalId(?int $sysInternalId): self
    {
        $this->sysInternalId = $sysInternalId;

        return $this;
    }

    public function getSysDataSentTo(): ?string
    {
        return $this->sysDataSentTo;
    }

    public function setSysDataSentTo(?string $sysDataSentTo): self
    {
        $this->sysDataSentTo = $sysDataSentTo;

        return $this;
    }

    public function getSysDataComeFrom(): ?string
    {
        return $this->sysDataComeFrom;
    }

    public function setSysDataComeFrom(?string $sysDataComeFrom): self
    {
        $this->sysDataComeFrom = $sysDataComeFrom;

        return $this;
    }

    public function getSysDataWorthSaving(): ?string
    {
        return $this->sysDataWorthSaving;
    }

    public function setSysDataWorthSaving(?string $sysDataWorthSaving): self
    {
        $this->sysDataWorthSaving = $sysDataWorthSaving;

        return $this;
    }

    public function getSysDataToScience(): ?string
    {
        return $this->sysDataToScience;
    }

    public function setSysDataToScience(?string $sysDataToScience): self
    {
        $this->sysDataToScience = $sysDataToScience;

        return $this;
    }

    public function getSysImpactAnalysisLink(): ?string
    {
        return $this->sysImpactAnalysisLink;
    }

    public function setSysImpactAnalysisLink(?string $sysImpactAnalysisLink): self
    {
        $this->sysImpactAnalysisLink = $sysImpactAnalysisLink;

        return $this;
    }

    public function getEDocUrl(): ?string
    {
        return $this->eDocUrl;
    }

    public function setEDocUrl(?string $eDocUrl): self
    {
        $this->eDocUrl = $eDocUrl;

        return $this;
    }

    /**
     * @return Collection<int, Group>
     */
    public function getGroups(): Collection
    {
        return $this->groups;
    }

    public function addGroup(Group $group): self
    {
        if (!$this->groups->contains($group)) {
            $this->groups->add($group);
        }

        return $this;
    }

    public function removeGroup(Group $group): self
    {
        $this->groups->removeElement($group);

        return $this;
    }
}
