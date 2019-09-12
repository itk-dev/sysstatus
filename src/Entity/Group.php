<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
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
     * @ORM\OneToMany(targetEntity="App\Entity\System", mappedBy="group")
     */
    private $systems;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Report", mappedBy="group")
     */
    private $reports;

    /**
     * @ORM\ManyToMany(targetEntity="App\Entity\Theme", mappedBy="groups")
     */
    private $themes;

    public function __construct()
    {
        parent::__construct(NULL, ['ROLE_USER']);
        $this->themes = new ArrayCollection();
    }

    public function __toString()
    {
        return $this->name;
    }

    /**
     * @return mixed
     */
    public function getSystems()
    {
        return $this->systems;
    }

    /**
     * @param mixed $systems
     */
    public function setSystems($systems): void
    {
        $this->systems = $systems;
    }

    /**
     * @return mixed
     */
    public function getReports()
    {
        return $this->reports;
    }

    /**
     * @param mixed $reports
     */
    public function setReports($reports): void
    {
        $this->reports = $reports;
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
}
