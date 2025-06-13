<?php

namespace App\Entity;

use App\Repository\SelfServiceAvailableFromItemRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Timestampable\Traits\TimestampableEntity;

#[ORM\Entity(repositoryClass: SelfServiceAvailableFromItemRepository::class)]
class SelfServiceAvailableFromItem implements \Stringable
{
    use TimestampableEntity;

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(nullable: true)]
    private ?int $id = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $name = null;

    /**
     * @var Collection<int, System>
     */
    #[ORM\ManyToMany(targetEntity: System::class, inversedBy: 'selfServiceAvailableFromItems')]
    private Collection $systems;

    public function __construct()
    {
        $this->systems = new ArrayCollection();
    }

    public function __toString(): string
    {
        return $this->name ?? self::class;
    }

    public function getId(): ?int
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
        }

        return $this;
    }

    public function removeSystem(System $system): self
    {
        $this->systems->removeElement($system);

        return $this;
    }
}
