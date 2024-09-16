<?php

namespace App\Entity;

use App\Repository\ThemeRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Blameable\Traits\BlameableEntity;
use Gedmo\Mapping\Annotation\Loggable;
use Gedmo\Mapping\Annotation\Versioned;
use Gedmo\Timestampable\Traits\TimestampableEntity;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

#[ORM\Entity(repositoryClass: ThemeRepository::class)]
#[Loggable]
#[UniqueEntity('name')]
class Theme
{
    use BlameableEntity;
    use TimestampableEntity;

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\OneToMany(mappedBy: 'theme', targetEntity: ThemeCategory::class, cascade: ['persist'], orphanRemoval: true)]
    private Collection $themeCategories;

    /**
     * @var ArrayCollection
     */
    private $categories;

    #[ORM\ManyToMany(targetEntity: Group::class, mappedBy: 'systemThemes')]
    private Collection $systemGroups;

    #[ORM\ManyToMany(targetEntity: Group::class, mappedBy: 'reportThemes')]
    private Collection $reportGroups;

    #[ORM\Column(length: 255)]
    #[Versioned]
    private ?string $name = null;

    public function __construct()
    {
        $this->categories = new ArrayCollection();
        $this->themeCategories = new ArrayCollection();
        $this->systemGroups = new ArrayCollection();
        $this->reportGroups = new ArrayCollection();
    }

    public function __toString()
    {
        return $this->getName() ?: $this->getId();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * @return Collection<int, ThemeCategory>
     */
    public function getThemeCategories(): Collection
    {
        return $this->themeCategories;
    }

    public function addThemeCategory(ThemeCategory $themeCategory): self
    {
        if (!$this->themeCategories->contains($themeCategory)) {
            $this->themeCategories->add($themeCategory);
            $themeCategory->setTheme($this);
        }

        return $this;
    }

    public function removeThemeCategory(ThemeCategory $themeCategory): self
    {
        if ($this->themeCategories->removeElement($themeCategory)) {
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
    public function getOrderedCategories(): array
    {
        $list = [];

        $themeCategories = $this->themeCategories;
        $iterator = $themeCategories->getIterator();
        $iterator->uasort(function ($first, $second) {
            return (int) $first->getSortOrder() < (int) $second->getSortOrder() ? 1 : -1;
        });

        foreach ($iterator as $i => $item) {
            $list[] = $item->getCategory();
        }

        return $list;
    }

    /**
     * @return Collection<int, Group>
     */
    public function getSystemGroups(): Collection
    {
        return $this->systemGroups;
    }

    public function addSystemGroup(Group $systemGroup): self
    {
        if (!$this->systemGroups->contains($systemGroup)) {
            $this->systemGroups->add($systemGroup);
            $systemGroup->addSystemTheme($this);
        }

        return $this;
    }

    public function removeSystemGroup(Group $systemGroup): self
    {
        if ($this->systemGroups->removeElement($systemGroup)) {
            $systemGroup->removeSystemTheme($this);
        }

        return $this;
    }

    /**
     * @return Collection<int, Group>
     */
    public function getReportGroups(): Collection
    {
        return $this->reportGroups;
    }

    public function addReportGroup(Group $reportGroup): self
    {
        if (!$this->reportGroups->contains($reportGroup)) {
            $this->reportGroups->add($reportGroup);
            $reportGroup->addReportTheme($this);
        }

        return $this;
    }

    public function removeReportGroup(Group $reportGroup): self
    {
        if ($this->reportGroups->removeElement($reportGroup)) {
            $reportGroup->removeSystemTheme($this);
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
}
