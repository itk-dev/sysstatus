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
                return sprintf('%s.sys_status = %s', $targetTableAlias, $this->getConnection()->quote('Aktiv'));
            case System::class:
                return sprintf('%s.sys_status <> %s', $targetTableAlias, $this->getConnection()->quote('Systemet bruges ikke længere'));
        }

        return '';
    }
}
