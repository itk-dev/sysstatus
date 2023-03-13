<?php

namespace App\Entity;

use App\Repository\GroupRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: GroupRepository::class)]
#[ORM\Table(name: '`group`')]
class Group
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToMany(targetEntity: Theme::class, mappedBy: 'systemGroups')]
    private Collection $systemThemes;

    #[ORM\ManyToMany(targetEntity: Theme::class, mappedBy: 'reportGroups')]
    private Collection $reportThemes;

    public function __construct()
    {
        $this->systemThemes = new ArrayCollection();
        $this->reportThemes = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * @return Collection<int, Theme>
     */
    public function getSystemThemes(): Collection
    {
        return $this->systemThemes;
    }

    public function addSystemTheme(Theme $systemTheme): self
    {
        if (!$this->systemThemes->contains($systemTheme)) {
            $this->systemThemes->add($systemTheme);
            $systemTheme->addSystemGroup($this);
        }

        return $this;
    }

    public function removeSystemTheme(Theme $systemTheme): self
    {
        if ($this->systemThemes->removeElement($systemTheme)) {
            $systemTheme->removeSystemGroup($this);
        }

        return $this;
    }

    /**
     * @return Collection<int, Theme>
     */
    public function getReportThemes(): Collection
    {
        return $this->reportThemes;
    }

    public function addReportTheme(Theme $reportTheme): self
    {
        if (!$this->reportThemes->contains($reportTheme)) {
            $this->reportThemes->add($reportTheme);
            $reportTheme->addReportGroup($this);
        }

        return $this;
    }

    public function removeReportTheme(Theme $reportTheme): self
    {
        if ($this->reportThemes->removeElement($reportTheme)) {
            $reportTheme->removeReportGroup($this);
        }

        return $this;
    }
}
