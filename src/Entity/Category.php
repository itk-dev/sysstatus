<?php

namespace App\Entity;

use App\Repository\CategoryRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Blameable\Traits\BlameableEntity;
use Gedmo\Mapping\Annotation\Loggable;
use Gedmo\Mapping\Annotation\Versioned;
use Gedmo\Timestampable\Traits\TimestampableEntity;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

#[ORM\Entity(repositoryClass: CategoryRepository::class)]
#[Loggable]
#[UniqueEntity('name')]
class Category implements \Stringable
{
    use BlameableEntity;
    use TimestampableEntity;

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(nullable: true)]
    private ?int $id = null;

    #[ORM\Column(length: 255, unique: true, nullable: true)]
    #[Versioned]
    private ?string $name = null;

    /**
     * @var Collection<int, ThemeCategory>
     */
    #[ORM\OneToMany(mappedBy: 'category', targetEntity: ThemeCategory::class, orphanRemoval: true)]
    private Collection $themeCategories;

    /**
     * @var Collection<int, Question>
     */
    #[ORM\OneToMany(mappedBy: 'category', targetEntity: Question::class, cascade: ['persist'])]
    private Collection $questions;

    public function __construct()
    {
        $this->themeCategories = new ArrayCollection();
        $this->questions = new ArrayCollection();
    }

    public function __toString(): string
    {
        return (string) ($this->getName() ?: $this->getId());
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

    /**
     * Virtual.
     *
     * @return array<Theme>
     *
     * @throws \Exception
     */
    public function getThemes(): array
    {
        $list = [];
        $iterator = $this->themeCategories->getIterator();

        foreach ($iterator as $item) {
            $list[$item->getTheme()->getId()] = $item->getTheme();
        }

        return $list;
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
     * @return Collection<int, Question>
     */
    public function getQuestions(): Collection
    {
        return $this->questions;
    }

    public function addQuestion(Question $question): self
    {
        if (!$this->questions->contains($question)) {
            $this->questions->add($question);
            $question->setCategory($this);
        }

        return $this;
    }

    public function removeQuestion(Question $question): self
    {
        if ($this->questions->removeElement($question)) {
            // set the owning side to null (unless already changed)
            if ($question->getCategory() === $this) {
                $question->setCategory(null);
            }
        }

        return $this;
    }
}
