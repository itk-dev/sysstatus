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
    #[ORM\JoinTable(name: "group_system_themes")]
    private Collection $systemThemes;

    #[ORM\ManyToMany(targetEntity: Theme::class, mappedBy: 'reportGroups')]
    #[ORM\JoinTable(name: "group_report_themes")]
    private Collection $reportThemes;

    #[ORM\ManyToMany(targetEntity: Report::class, mappedBy: 'groups')]
    private Collection $reports;

    #[ORM\ManyToMany(targetEntity: System::class, mappedBy: 'groups')]
    private Collection $systems;

    #[ORM\ManyToMany(targetEntity: User::class, mappedBy: 'groups')]
    private Collection $users;

    public function __construct()
    {
        $this->systemThemes = new ArrayCollection();
        $this->reportThemes = new ArrayCollection();
        $this->reports = new ArrayCollection();
        $this->systems = new ArrayCollection();
        $this->users = new ArrayCollection();
    }

    public function __toString()
    {
        return $this->name;
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

    /**
     * @return Collection<int, Report>
     */
    public function getReports(): Collection
    {
        return $this->reports;
    }

    public function addReport(Report $report): self
    {
        if (!$this->reports->contains($report)) {
            $this->reports->add($report);
            $report->addGroup($this);
        }

        return $this;
    }

    public function removeReport(Report $report): self
    {
        if ($this->reports->removeElement($report)) {
            $report->removeGroup($this);
        }

        return $this;
    }

    /**
     * @return Collection<int, System>
     */
    public function getSystems(): Collection
    {
        return $this->systems;
    }

    public function addSystem(System $system): self
    {
        if (!$this->systems->contains($system)) {
            $this->systems->add($system);
            $system->addGroup($this);
        }

        return $this;
    }

    public function removeSystem(System $system): self
    {
        if ($this->systems->removeElement($system)) {
            $system->removeGroup($this);
        }

        return $this;
    }

    /**
     * @return Collection<int, User>
     */
    public function getUsers(): Collection
    {
        return $this->users;
    }

    public function addUser(User $user): self
    {
        if (!$this->users->contains($user)) {
            $this->users->add($user);
            $user->addGroup($this);
        }

        return $this;
    }

    public function removeUser(User $user): self
    {
        if ($this->users->removeElement($user)) {
            $user->removeGroup($this);
        }

        return $this;
    }
}
