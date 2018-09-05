<?php

namespace App\Tests\Unit\Entity;

use App\Entity\User;
use App\Entity\UserRole;
use PHPUnit\Framework\TestCase;

class UserRoleTest extends TestCase
{
    private const SOME_NAME = 'someName';
    private const SOME_CODE = 'someCode';

    /**
     * @var UserRole
     */
    private $sut;

    protected function setUp(): void
    {
        $this->sut = new UserRole();
    }

    public function testSetterAndGetter()
    {
        $this->sut->setName(self::SOME_NAME)
            ->setCode(self::SOME_CODE)
            ->setUsers([]);

        $dummyUser = new User();

        $this->sut->addUser($dummyUser);
        self::assertNotEmpty($this->sut->getUsers());
        self::assertCount(1, $this->sut->getUsers());
        $this->sut->removeUser($dummyUser);

        self::assertSame(self::SOME_NAME, $this->sut->getName());
        self::assertSame(self::SOME_CODE, $this->sut->getCode());
        self::assertSame(self::SOME_CODE, $this->sut->getId());
        self::assertEmpty($this->sut->getUsers());
        self::assertCount(0, $this->sut->getUsers());
    }
}
