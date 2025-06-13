<?php

namespace App\Traits;

use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

trait ArchivableEntity
{
    #[ORM\Column(type: Types::DATETIME_MUTABLE, nullable: true)]
    protected ?\DateTime $archivedAt = null;

    /**
     * Sets archivedAt.
     *
     * @return $this
     */
    public function setArchivedAt(?\DateTime $archivedAt = null)
    {
        $this->archivedAt = $archivedAt;

        return $this;
    }

    /**
     * Returns archivedAt.
     *
     * @return \DateTime
     */
    public function getArchivedAt()
    {
        return $this->archivedAt;
    }

    /**
     * Is archived?
     *
     * @return bool
     */
    public function isArchived()
    {
        return null !== $this->archivedAt;
    }
}
