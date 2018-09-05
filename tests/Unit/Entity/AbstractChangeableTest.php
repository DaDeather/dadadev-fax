<?php

namespace App\Tests\Unit\Entity;

use App\Entity\AbstractChangeable;
use App\Entity\UserRole;
use PHPUnit\Framework\TestCase;

class AbstractChangeableTest extends TestCase
{
    /**
     * @var AbstractChangeable
     */
    private $sut;

    protected function setUp(): void
    {
        $this->sut = new UserRole();
    }

    public function testSetterAndGetter()
    {
        $datetime = new \DateTime();
        $this->sut->setCreated($datetime)
            ->setDeleted($datetime)
            ->setUpdated($datetime);

        self::assertSame($datetime, $this->sut->getCreated());
        self::assertSame($datetime, $this->sut->getDeleted());
        self::assertSame($datetime, $this->sut->getUpdated());

        $this->sut->preUpdateSetUpdatedValue();
        self::assertInstanceOf(\DateTime::class, $this->sut->getUpdated());
        self::assertNotSame($datetime, $this->sut->getUpdated());
    }
}
