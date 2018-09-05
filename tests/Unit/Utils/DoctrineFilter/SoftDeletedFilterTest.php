<?php

namespace App\Tests\Unit\Utils\DoctrineFilter;

use App\Entity\AbstractChangeable;
use App\Utils\DoctrineFilter\SoftDeletedFilter;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Mapping\ClassMetadata;
use PHPUnit\Framework\TestCase;

class SoftDeletedFilterTest extends TestCase
{
    /** @var SoftDeletedFilter */
    private $sut;

    /** @var EntityManagerInterface|\PHPUnit_Framework_MockObject_MockObject */
    private $entityManagerMock;

    protected function setUp()
    {
        $this->entityManagerMock = $this->getMockBuilder(EntityManagerInterface::class)->getMock();

        $this->sut = new SoftDeletedFilter($this->entityManagerMock);
    }

    public function testAddFilterConstraintAddingIsDeleted(): void
    {
        /** @var ClassMetadata|\PHPUnit_Framework_MockObject_MockObject $classMetadataMock */
        $classMetadataMock = $this->createMock(ClassMetadata::class);

        /** @var \ReflectionClass|\PHPUnit_Framework_MockObject_MockObject $reflectionClassMock */
        $reflectionClassMock = $this->createMock(\ReflectionClass::class);
        $reflectionClassMock->method('getParentClass')->willReturn($reflectionClassMock);
        $reflectionClassMock->method('getName')->willReturn(AbstractChangeable::class);

        $classMetadataMock->reflClass = $reflectionClassMock;

        $result = $this->sut->addFilterConstraint($classMetadataMock, 'someTargetTableAlias');

        self::assertSame('someTargetTableAlias.deleted IS NULL', $result);
    }

    public function testAddFilterConstraintNotAdding(): void
    {
        /** @var ClassMetadata|\PHPUnit_Framework_MockObject_MockObject $classMetadataMock */
        $classMetadataMock = $this->createMock(ClassMetadata::class);

        /** @var \ReflectionClass|\PHPUnit_Framework_MockObject_MockObject $reflectionClassMock */
        $reflectionClassMock = $this->createMock(\ReflectionClass::class);
        $reflectionClassMock->method('getParentClass')->willReturn(false);

        $classMetadataMock->reflClass = $reflectionClassMock;

        $result = $this->sut->addFilterConstraint($classMetadataMock, 'someTargetTableAlias');

        self::assertSame('', $result);
    }
}
