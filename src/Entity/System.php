<?php

namespace App\Entity;

use App\Repository\SystemRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: SystemRepository::class)]
class System
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToMany(targetEntity: SelfServiceAvailableFromItem::class, mappedBy: 'systems')]
    private Collection $selfServiceAvailableFromItems;

    public function __construct()
    {
        $this->selfServiceAvailableFromItems = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * @return Collection<int, SelfServiceAvailableFromItem>
     */
    public function getSelfServiceAvailableFromItems(): Collection
    {
        return $this->selfServiceAvailableFromItems;
    }

    public function addSelfServiceAvailableFromItem(SelfServiceAvailableFromItem $selfServiceAvailableFromItem): self
    {
        if (!$this->selfServiceAvailableFromItems->contains($selfServiceAvailableFromItem)) {
            $this->selfServiceAvailableFromItems->add($selfServiceAvailableFromItem);
            $selfServiceAvailableFromItem->addSystem($this);
        }

        return $this;
    }

    public function removeSelfServiceAvailableFromItem(SelfServiceAvailableFromItem $selfServiceAvailableFromItem): self
    {
        if ($this->selfServiceAvailableFromItems->removeElement($selfServiceAvailableFromItem)) {
            $selfServiceAvailableFromItem->removeSystem($this);
        }

        return $this;
    }
}
