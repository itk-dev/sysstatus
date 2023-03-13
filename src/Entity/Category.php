<?php

namespace App\Entity;

use App\Repository\CategoryRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: CategoryRepository::class)]
class Category
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\OneToMany(mappedBy: 'category', targetEntity: ThemeCategory::class, orphanRemoval: true)]
    private Collection $themeCategories;

    public function __construct()
    {
        $this->themeCategories = new ArrayCollection();
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
            $themeCategory->setCategory($this);
        }

        return $this;
    }

    public function removeThemeCategory(ThemeCategory $themeCategory): self
    {
        if ($this->themeCategories->removeElement($themeCategory)) {
            // set the owning side to null (unless already changed)
            if ($themeCategory->getCategory() === $this) {
                $themeCategory->setCategory(null);
            }
        }

        return $this;
    }
}
