<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Blameable\Traits\BlameableEntity;
use Gedmo\Timestampable\Traits\TimestampableEntity;
use Gedmo\Mapping\Annotation as Gedmo;

/**
 * @ORM\Entity(repositoryClass="App\Repository\CategoryRepository")
 * @Gedmo\Loggable
 */
class Category
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
     * @ORM\OneToMany(targetEntity="App\Entity\Question", mappedBy="category")
     */
    private $questions;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\ThemeCategory", mappedBy="category", orphanRemoval=true)
     */
    private $themeCategories;

    public function __construct()
    {
        $this->questions = new ArrayCollection();
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
     * @return Collection|Question[]
     */
    public function getQuestions(): Collection
    {
        return $this->questions;
    }

    public function addQuestion(Question $question): self
    {
        if (!$this->questions->contains($question)) {
            $this->questions[] = $question;
            $question->setCategory($this);
        }

        return $this;
    }

    public function removeQuestion(Question $question): self
    {
        if ($this->questions->contains($question)) {
            $this->questions->removeElement($question);
            // set the owning side to null (unless already changed)
            if ($question->getCategory() === $this) {
                $question->setCategory(null);
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
            $themeCategory->setCategory($this);
        }

        return $this;
    }

    public function removeThemeCategory(ThemeCategory $themeCategory): self
    {
        if ($this->themeCategories->contains($themeCategory)) {
            $this->themeCategories->removeElement($themeCategory);
            // set the owning side to null (unless already changed)
            if ($themeCategory->getCategory() === $this) {
                $themeCategory->setCategory(null);
            }
        }

        return $this;
    }

    /**
     * Virtual.
     */
    public function getThemes() {
        $list = [];
        $iterator = $this->themeCategories->getIterator();

        foreach($iterator as $i => $item) {
            $list[$item->getTheme()->getId()] = $item->getTheme();
        }

        return $list;
    }
}
