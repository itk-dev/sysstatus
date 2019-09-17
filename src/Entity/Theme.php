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
     * @ORM\OneToMany(targetEntity="App\Entity\ThemeCategory", mappedBy="theme", orphanRemoval=true)
     */
    private $themeCategories;

    /**
     * @var \Doctrine\Common\Collections\ArrayCollection
     */
    private $categories;

    /**
     * @ORM\ManyToMany(targetEntity="App\Entity\Group", inversedBy="themes")
     */
    private $groups;

    public function __construct()
    {
        $this->categories = new ArrayCollection();
        $this->themeCategories = new ArrayCollection();
        $this->groups = new ArrayCollection();
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
    public function getGroups(): Collection
    {
        return $this->groups;
    }

    public function addGroups(Group $groups): self
    {
        if (!$this->groups->contains($groups)) {
            $this->groups[] = $groups;
        }

        return $this;
    }

    public function removeGroups(Group $groups): self
    {
        if ($this->groups->contains($groups)) {
            $this->groups->removeElement($groups);
        }

        return $this;
    }
}
