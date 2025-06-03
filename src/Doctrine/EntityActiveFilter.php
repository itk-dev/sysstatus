<?php

namespace App\Doctrine;

use Doctrine\ORM\Mapping\ClassMetadata;
use Doctrine\ORM\Query\Filter\SQLFilter;

class EntityActiveFilter extends SQLFilter
{
    public function addFilterConstraint(ClassMetadata $targetEntity, $targetTableAlias): string
    {
        if ($targetEntity->hasField('sysStatus')) {
            return sprintf('%s.sys_status = %s', $targetTableAlias, $this->getParameter('active_value'));
        }

        return '';
    }
}
