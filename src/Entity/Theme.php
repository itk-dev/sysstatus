<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Blameable\Traits\BlameableEntity;
use Gedmo\Timestampable\Traits\TimestampableEntity;
use Gedmo\Mapping\Annotation as Gedmo;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

/**
 * @ORM\Entity(repositoryClass="App\Repository\ThemeRepository")
 * @Gedmo\Loggable
 * @UniqueEntity("name")
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
     * @ORM\Column(type="string", length=255, unique=true))
     * @Gedmo\Versioned
     */
    private $name;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\ThemeCategory", mappedBy="theme", orphanRemoval=true, cascade={"persist"})
     */
    private $themeCategories;

    /**
     * @var \Doctrine\Common\Collections\ArrayCollection
     */
    private $categories;

    /**
     * @ORM\ManyToMany(targetEntity="App\Entity\Group", mappedBy="systemThemes")
     */
    private $systemGroups;

    /**
     * @ORM\ManyToMany(targetEntity="App\Entity\Group", mappedBy="reportThemes")
     */
    private $reportGroups;

    public function __construct()
    {
        $this->categories = new ArrayCollection();
        $this->themeCategories = new ArrayCollection();
        $this->groups = new ArrayCollection();
        $this->systemGroups = new ArrayCollection();
        $this->reportGroups = new ArrayCollection();
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

    /**
     * @return Collection|Group[]
     */
    public function getSystemGroups(): Collection
    {
        return $this->systemGroups;
    }

    public function addSystemGroup(Group $systemGroup): self
    {
        if (!$this->systemGroups->contains($systemGroup)) {
            $this->systemGroups[] = $systemGroup;
            $systemGroup->addSystemTheme($this);
        }

        return $this;
    }

    public function removeSystemGroup(Group $systemGroup): self
    {
        if ($this->systemGroups->contains($systemGroup)) {
            $this->systemGroups->removeElement($systemGroup);
            $systemGroup->removeSystemTheme($this);
        }

        return $this;
    }

    /**
     * @return Collection|Group[]
     */
    public function getReportGroups(): Collection
    {
        return $this->reportGroups;
    }

    public function addReportGroup(Group $reportGroup): self
    {
        if (!$this->reportGroups->contains($reportGroup)) {
            $this->reportGroups[] = $reportGroup;
            $reportGroup->addReportTheme($this);
        }

        return $this;
    }

    public function removeReportGroup(Group $reportGroup): self
    {
        if ($this->reportGroups->contains($reportGroup)) {
            $this->reportGroups->removeElement($reportGroup);
            $reportGroup->removeReportTheme($this);
        }

        return $this;
    }
}
