<?php

namespace App\Entity;

use Doctrine\Common\Collections\Collection;
use FOS\UserBundle\Model\GroupInterface;
use FOS\UserBundle\Model\User as BaseUser;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Blameable\Traits\BlameableEntity;
use Gedmo\Timestampable\Traits\TimestampableEntity;

/**
 * @ORM\Entity
 * @ORM\Table(name="fos_user")
 */
class User extends BaseUser
{
    use BlameableEntity;
    use TimestampableEntity;

    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @ORM\ManyToMany(targetEntity="App\Entity\Group", inversedBy="users")
     * @ORM\JoinTable(name="fos_user_user_group")
     */
    protected $groups;

    public function __construct()
    {
        parent::__construct();
    }

    /**
     * @return Collection|Group[]
     */
    public function getGroups()
    {
        return $this->groups;
    }

    /**
     * @param \Doctrine\Common\Collections\Collection $groups
     */
    public function setGroups($groups): void
    {
        $this->groups = $groups;
    }

    public function addGroup(GroupInterface $group): self
    {
        if (!$this->groups->contains($group)) {
            $this->groups[] = $group;
            $group->addGroup($this);
        }

        return $this;
    }

    public function removeGroup(GroupInterface $group): self
    {
        if ($this->groups->contains($group)) {
            $this->groups->removeElement($group);
            $group->removeGroup($this);
        }

        return $this;
    }
}
