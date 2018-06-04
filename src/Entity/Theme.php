<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Blameable\Traits\BlameableEntity;
use Gedmo\Timestampable\Traits\TimestampableEntity;
use Gedmo\Mapping\Annotation as Gedmo;

/**
 * @ORM\Entity(repositoryClass="App\Repository\ThemeRepository")
 * @Gedmo\Loggable
 */
class Theme
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
     * @Gedmo\Versioned
     */
    private $name;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\System", mappedBy="theme")
     */
    private $systems;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Report", mappedBy="theme")
     */
    private $reports;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\ThemeCategory", mappedBy="theme", orphanRemoval=true)
     */
    private $themeCategories;

    /**
     * @var \Doctrine\Common\Collections\ArrayCollection
     */
    private $categories;

    public function __construct()
    {
        $this->systems = new ArrayCollection();
        $this->reports = new ArrayCollection();
        $this->categories = new ArrayCollection();
        $this->themeCategories = new ArrayCollection();
    }

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
            $system->setTheme($this);
        }

        return $this;
    }

    public function removeSystem(System $system): self
    {
        if ($this->systems->contains($system)) {
            $this->systems->removeElement($system);
            // set the owning side to null (unless already changed)
            if ($system->getTheme() === $this) {
                $system->setTheme(null);
            }
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
            $report->setTheme($this);
        }

        return $this;
    }

    public function removeReport(Report $report): self
    {
        if ($this->reports->contains($report)) {
            $this->reports->removeElement($report);
            // set the owning side to null (unless already changed)
            if ($report->getTheme() === $this) {
                $report->setTheme(null);
            }
        }

        return $this;
    }

    public function __toString()
    {
        return $this->getName() ?: $this->getId();
    }

    /**
     * @return Collection|ThemeCategory[]
     */
    public function getThemeCategories(): Collection
    {
        return $this->themeCategories;
    }

    public function addThemeCategory(ThemeCategory $themeCategory): self
    {
        if (!$this->themeCategories->contains($themeCategory)) {
            $this->themeCategories[] = $themeCategory;
            $themeCategory->setTheme($this);
        }

        return $this;
    }

    public function removeThemeCategory(ThemeCategory $themeCategory): self
    {
        if ($this->themeCategories->contains($themeCategory)) {
            $this->themeCategories->removeElement($themeCategory);
            // set the owning side to null (unless already changed)
            if ($themeCategory->getTheme() === $this) {
                $themeCategory->setTheme(null);
            }
        }

        return $this;
    }

    /**
     * Virtual.
     */
    public function getOrderedCategories() {
        $list = [];

        $themeCategories = $this->themeCategories;
        $iterator = $themeCategories->getIterator();
        $iterator->uasort(function ($first, $second) {
            return (int) $first->getSortOrder() < (int) $second->getSortOrder() ? 1 : -1;
        });

        foreach($iterator as $i => $item) {
            $list[] = $item->getCategory();
        }

        return $list;
    }
}
