<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping\JoinTable;
use FOS\UserBundle\Model\Group as BaseGroup;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name="fos_group")
 */
class Group extends BaseGroup
{
    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @ORM\ManyToMany(targetEntity="App\Entity\Theme", mappedBy="groups")
     */
    private $themes;

    /**
     * @ORM\ManyToMany(targetEntity="App\Entity\Report", mappedBy="groups")
     */
    private $reports;

    /**
     * @ORM\ManyToMany(targetEntity="App\Entity\System", mappedBy="groups")
     */
    private $systems;

    /**
     * @ORM\ManyToMany(targetEntity="App\Entity\Theme", inversedBy="systemGroups")
     * @JoinTable(name="group_system_themes")
     */
    private $systemThemes;

    /**
     * @ORM\ManyToMany(targetEntity="App\Entity\Theme", inversedBy="reportGroups")
     * @JoinTable(name="group_report_themes")
     */
    private $reportThemes;

    public function __construct()
    {
        parent::__construct(NULL, ['ROLE_USER']);
        $this->themes = new ArrayCollection();
        $this->reports = new ArrayCollection();
        $this->systems = new ArrayCollection();
        $this->systemThemes = new ArrayCollection();
        $this->reportThemes = new ArrayCollection();
    }

    public function __toString()
    {
        return $this->name;
    }

    /**
     * @return Collection|Theme[]
     */
    public function getThemes(): Collection
    {
        return $this->themes;
    }

    public function addTheme(Theme $theme): self
    {
        if (!$this->themes->contains($theme)) {
            $this->themes[] = $theme;
            $theme->addGroup($this);
        }

        return $this;
    }

    public function removeTheme(Theme $theme): self
    {
        if ($this->themes->contains($theme)) {
            $this->themes->removeElement($theme);
            $theme->removeGroup($this);
        }

        return $this;
    }

    /**
     * @return Collection|Report[]
     */
    public function getReports(): Collection
    {
        return $this->reports;
    }

    public function addReport(Report $report): self
    {
        if (!$this->reports->contains($report)) {
            $this->reports[] = $report;
            $report->addGroup($this);
        }

        return $this;
    }

    public function removeReport(Report $report): self
    {
        if ($this->reports->contains($report)) {
            $this->reports->removeElement($report);
            $report->removeGroup($this);
        }

        return $this;
    }

    /**
     * @return Collection|System[]
     */
    public function getSystems(): Collection
    {
        return $this->systems;
    }

    public function addSystem(System $system): self
    {
        if (!$this->systems->contains($system)) {
            $this->systems[] = $system;
            $system->addGroup($this);
        }

        return $this;
    }

    public function removeSystem(System $system): self
    {
        if ($this->systems->contains($system)) {
            $this->systems->removeElement($system);
            $system->removeGroup($this);
        }

        return $this;
    }

    /**
     * @return Collection|Theme[]
     */
    public function getSystemThemes(): Collection
    {
        return $this->systemThemes;
    }

    public function addSystemTheme(Theme $systemTheme): self
    {
        if (!$this->systemThemes->contains($systemTheme)) {
            $this->systemThemes[] = $systemTheme;
        }

        return $this;
    }

    public function removeSystemTheme(Theme $systemTheme): self
    {
        if ($this->systemThemes->contains($systemTheme)) {
            $this->systemThemes->removeElement($systemTheme);
        }

        return $this;
    }

    /**
     * @return Collection|Theme[]
     */
    public function getReportThemes(): Collection
    {
        return $this->reportThemes;
    }

    public function addReportTheme(Theme $reportTheme): self
    {
        if (!$this->reportThemes->contains($reportTheme)) {
            $this->reportThemes[] = $reportTheme;
        }

        return $this;
    }

    public function removeReportTheme(Theme $reportTheme): self
    {
        if ($this->reportThemes->contains($reportTheme)) {
            $this->reportThemes->removeElement($reportTheme);
        }

        return $this;
    }
}
