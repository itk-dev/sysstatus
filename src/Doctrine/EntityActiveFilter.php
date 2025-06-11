<?php

namespace App\Doctrine;

use App\Entity\Report;
use App\Entity\System;
use Doctrine\ORM\Mapping\ClassMetadata;
use Doctrine\ORM\Query\Filter\SQLFilter;

class EntityActiveFilter extends SQLFilter
{
    public function addFilterConstraint(ClassMetadata $targetEntity, $targetTableAlias): string
    {
        switch ($targetEntity->getName()) {
            case Report::class:
                return sprintf(
                    '(%1$s.archived_at IS NULL AND %1$s.sys_status = %2$s)',
                    $targetTableAlias,
                    $this->getConnection()->quote(Report::STATUS_ACTIVE)
                );
            case System::class:
                return sprintf(
                    '(%1$s.archived_at IS NULL AND %1$s.sys_status <> %2$s)',
                    $targetTableAlias,
                    $this->getConnection()->quote(System::STATUS_NOT_ACTIVE)
                );
        }

        return '';
    }
}
