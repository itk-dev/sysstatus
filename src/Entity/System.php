<?php

namespace App\Entity;

use App\Repository\SystemRepository;
use App\Traits\ArchivableEntity;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Blameable\Traits\BlameableEntity;
use Gedmo\Mapping\Annotation\Loggable;
use Gedmo\Mapping\Annotation\Versioned;
use Gedmo\Timestampable\Traits\TimestampableEntity;

#[ORM\Entity(repositoryClass: SystemRepository::class)]
#[Loggable]
class System implements \Stringable
{
    use BlameableEntity;
    use TimestampableEntity;
    use ArchivableEntity;

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(nullable: true)]
    private ?int $id = null;

    /**
     * @var Collection<int, SelfServiceAvailableFromItem>
     */
    #[ORM\ManyToMany(targetEntity: SelfServiceAvailableFromItem::class, mappedBy: 'systems')]
    private Collection $selfServiceAvailableFromItems;

    /**
     * @var Collection<int, Answer>
     */
    #[ORM\OneToMany(mappedBy: 'system', targetEntity: Answer::class)]
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

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $sysOwner = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $sysOwnerSubdepartment = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $sysEmergencySetup = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $sysContractor = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $sysUrgencyRating = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $sysTechnicalDocumentation = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $sysExternalDependencies = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $sysImportantInformation = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $sysSuperuserOrganization = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $sysServerNames = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $sysITSecurityCategory = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $sysLinkToSecurityReview = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $sysLinkToContract = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE, nullable: true)]
    private ?\DateTimeInterface $sysEndOfContract = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $sysOpenSource = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $sysDigitalPost = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $sysSystemCategory = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $sysDigitalTransactionsPrYear = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $sysTotalTransactionsPrYear = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $sysSelfServiceURL = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $sysVersion = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $sysOwnerSub = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $sysLink = null;

    #[ORM\Column(nullable: true)]
    private ?int $sysInternalId = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $sysStatus = null;

    #[ORM\Column(name: 'edoc_url', length: 255, nullable: true)]
    private ?string $eDocUrl = null;

    /**
     * @var Collection<int, UserGroup>
     */
    #[ORM\ManyToMany(targetEntity: UserGroup::class, inversedBy: 'systems')]
    private Collection $groups;

    #[ORM\Column(type: Types::DATETIME_MUTABLE, nullable: true)]
    private ?\DateTimeInterface $sysUpdated = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $sysDescription = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $sysOpenData = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $sysNumberOfUsers = null;

    public function __construct()
    {
        $this->selfServiceAvailableFromItems = new ArrayCollection();
        $this->answers = new ArrayCollection();
        $this->groups = new ArrayCollection();
    }

    public function __toString(): string
    {
        return (string) $this->getName();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * @return Collection<int, SelfServiceAvailableFromItem>
     */
    public function getSelfServiceAvailableFromItems(): Collection
    {
        return $this->selfServiceAvailableFromItems;
    }

    public function addSelfServiceAvailableFromItem(SelfServiceAvailableFromItem $selfServiceAvailableFromItem): self
    {
        if (!$this->selfServiceAvailableFromItems->contains($selfServiceAvailableFromItem)) {
            $this->selfServiceAvailableFromItems->add($selfServiceAvailableFromItem);
            $selfServiceAvailableFromItem->addSystem($this);
        }

        return $this;
    }

    public function removeSelfServiceAvailableFromItem(SelfServiceAvailableFromItem $selfServiceAvailableFromItem): self
    {
        if ($this->selfServiceAvailableFromItems->removeElement($selfServiceAvailableFromItem)) {
            $selfServiceAvailableFromItem->removeSystem($this);
        }

        return $this;
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
            $answer->setSystem($this);
        }

        return $this;
    }

    public function removeAnswer(Answer $answer): self
    {
        if ($this->answers->removeElement($answer)) {
            // set the owning side to null (unless already changed)
            if ($answer->getSystem() === $this) {
                $answer->setSystem(null);
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

    public function getSysOwner(): ?string
    {
        return $this->sysOwner;
    }

    public function setSysOwner(?string $sysOwner): self
    {
        $this->sysOwner = $sysOwner;

        return $this;
    }

    public function getSysOwnerSubdepartment(): ?string
    {
        return $this->sysOwnerSubdepartment;
    }

    public function setSysOwnerSubdepartment(?string $sysOwnerSubdepartment): self
    {
        $this->sysOwnerSubdepartment = $sysOwnerSubdepartment;

        return $this;
    }

    public function getSysEmergencySetup(): ?string
    {
        return $this->sysEmergencySetup;
    }

    public function setSysEmergencySetup(?string $sysEmergencySetup): self
    {
        $this->sysEmergencySetup = $sysEmergencySetup;

        return $this;
    }

    public function getSysContractor(): ?string
    {
        return $this->sysContractor;
    }

    public function setSysContractor(?string $sysContractor): self
    {
        $this->sysContractor = $sysContractor;

        return $this;
    }

    public function getSysUrgencyRating(): ?string
    {
        return $this->sysUrgencyRating;
    }

    public function setSysUrgencyRating(?string $sysUrgencyRating): self
    {
        $this->sysUrgencyRating = $sysUrgencyRating;

        return $this;
    }

    public function getSysTechnicalDocumentation(): ?string
    {
        return $this->sysTechnicalDocumentation;
    }

    public function setSysTechnicalDocumentation(?string $sysTechnicalDocumentation): self
    {
        $this->sysTechnicalDocumentation = $sysTechnicalDocumentation;

        return $this;
    }

    public function getSysExternalDependencies(): ?string
    {
        return $this->sysExternalDependencies;
    }

    public function setSysExternalDependencies(?string $sysExternalDependencies): self
    {
        $this->sysExternalDependencies = $sysExternalDependencies;

        return $this;
    }

    public function getSysImportantInformation(): ?string
    {
        return $this->sysImportantInformation;
    }

    public function setSysImportantInformation(?string $sysImportantInformation): self
    {
        $this->sysImportantInformation = $sysImportantInformation;

        return $this;
    }

    public function getSysSuperuserOrganization(): ?string
    {
        return $this->sysSuperuserOrganization;
    }

    public function setSysSuperuserOrganization(?string $sysSuperuserOrganization): self
    {
        $this->sysSuperuserOrganization = $sysSuperuserOrganization;

        return $this;
    }

    public function getSysServerNames(): ?string
    {
        return $this->sysServerNames;
    }

    public function setSysServerNames(?string $sysServerNames): self
    {
        $this->sysServerNames = $sysServerNames;

        return $this;
    }

    public function getSysITSecurityCategory(): ?string
    {
        return $this->sysITSecurityCategory;
    }

    public function setSysITSecurityCategory(?string $sysITSecurityCategory): self
    {
        $this->sysITSecurityCategory = $sysITSecurityCategory;

        return $this;
    }

    public function getSysLinkToSecurityReview(): ?string
    {
        return $this->sysLinkToSecurityReview;
    }

    public function setSysLinkToSecurityReview(?string $sysLinkToSecurityReview): self
    {
        $this->sysLinkToSecurityReview = $sysLinkToSecurityReview;

        return $this;
    }

    public function getSysLinkToContract(): ?string
    {
        return $this->sysLinkToContract;
    }

    public function setSysLinkToContract(?string $sysLinkToContract): self
    {
        $this->sysLinkToContract = $sysLinkToContract;

        return $this;
    }

    public function getSysEndOfContract(): ?\DateTimeInterface
    {
        return $this->sysEndOfContract;
    }

    public function setSysEndOfContract(?\DateTimeInterface $sysEndOfContract): self
    {
        $this->sysEndOfContract = $sysEndOfContract;

        return $this;
    }

    public function getSysOpenSource(): ?string
    {
        return $this->sysOpenSource;
    }

    public function setSysOpenSource(string $sysOpenSource): self
    {
        $this->sysOpenSource = $sysOpenSource;

        return $this;
    }

    public function getSysDigitalPost(): ?string
    {
        return $this->sysDigitalPost;
    }

    public function setSysDigitalPost(?string $sysDigitalPost): self
    {
        $this->sysDigitalPost = $sysDigitalPost;

        return $this;
    }

    public function getSysSystemCategory(): ?string
    {
        return $this->sysSystemCategory;
    }

    public function setSysSystemCategory(?string $sysSystemCategory): self
    {
        $this->sysSystemCategory = $sysSystemCategory;

        return $this;
    }

    public function getSysDigitalTransactionsPrYear(): ?string
    {
        return $this->sysDigitalTransactionsPrYear;
    }

    public function setSysDigitalTransactionsPrYear(?string $sysDigitalTransactionsPrYear): self
    {
        $this->sysDigitalTransactionsPrYear = $sysDigitalTransactionsPrYear;

        return $this;
    }

    public function getSysTotalTransactionsPrYear(): ?string
    {
        return $this->sysTotalTransactionsPrYear;
    }

    public function setSysTotalTransactionsPrYear(?string $sysTotalTransactionsPrYear): self
    {
        $this->sysTotalTransactionsPrYear = $sysTotalTransactionsPrYear;

        return $this;
    }

    public function getSysSelfServiceURL(): ?string
    {
        return $this->sysSelfServiceURL;
    }

    public function setSysSelfServiceURL(?string $sysSelfServiceURL): self
    {
        $this->sysSelfServiceURL = $sysSelfServiceURL;

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

    public function getSysOwnerSub(): ?string
    {
        return $this->sysOwnerSub;
    }

    public function setSysOwnerSub(?string $sysOwnerSub): self
    {
        $this->sysOwnerSub = $sysOwnerSub;

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

    public function getSysStatus(): ?string
    {
        return $this->sysStatus;
    }

    public function setSysStatus(?string $sysStatus): self
    {
        $this->sysStatus = $sysStatus;

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
     * @return Collection<int, UserGroup>
     */
    public function getGroups(): Collection
    {
        return $this->groups;
    }

    public function addGroup(UserGroup $group): self
    {
        if (!$this->groups->contains($group)) {
            $this->groups->add($group);
        }

        return $this;
    }

    public function removeGroup(UserGroup $group): self
    {
        $this->groups->removeElement($group);

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

    public function getSysDescription(): ?string
    {
        return $this->sysDescription;
    }

    public function setSysDescription(?string $sysDescription): self
    {
        $this->sysDescription = $sysDescription;

        return $this;
    }

    public function getSysOpenData(): ?string
    {
        return $this->sysOpenData;
    }

    public function setSysOpenData(?string $sysOpenData): self
    {
        $this->sysOpenData = $sysOpenData;

        return $this;
    }

    /**
     * Virtual property.
     */
    public function getSysIdAsLink(): ?string
    {
        return $this->getSysId();
    }

    /**
     * Virtual property.
     */
    public function getShowableName(): ?string
    {
        return $this->sysTitle ?? $this->getName();
    }

    /**
     * Virtual property.
     */
    public function getTextSet(): bool
    {
        return isset($this->text);
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
            $themes = array_merge($themes, $group->getSystemThemes()->toArray());
        }

        return $themes;
    }

    public function setSysNumberOfUsers(string $sysNumberOfUsers): self
    {
        $this->sysNumberOfUsers = $sysNumberOfUsers;

        return $this;
    }

    public function getSysNumberOfUsers(): ?string
    {
        return $this->sysNumberOfUsers;
    }
}
