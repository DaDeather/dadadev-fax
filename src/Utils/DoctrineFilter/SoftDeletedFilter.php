<?php

namespace App\Utils\DoctrineFilter;

use App\Entity\AbstractChangeable;
use Doctrine\ORM\Mapping\ClassMetadata;
use Doctrine\ORM\Query\Filter\SQLFilter;

class SoftDeletedFilter extends SQLFilter
{
    /**
     * Gets the SQL query part to add to a query.
     *
     * @param ClassMetaData $targetEntity
     * @param string        $targetTableAlias
     *
     * @return string the constraint SQL if there is available, empty string otherwise
     */
    public function addFilterConstraint(ClassMetadata $targetEntity, $targetTableAlias)
    {
        /** @var \ReflectionClass $parentClass */
        $parentClass = $targetEntity->reflClass->getParentClass();

        if (false !== $parentClass && AbstractChangeable::class == $parentClass->getName()) {
            return $targetTableAlias . '.deleted IS NULL';
        }

        return '';
    }
}
