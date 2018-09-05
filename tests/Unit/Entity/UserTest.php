<?php

namespace App\Tests\Unit\Entity;

use App\Entity\User;
use App\Entity\UserRole;
use PHPUnit\Framework\TestCase;

class UserTest extends TestCase
{
    private const SOME_EMAIL = 'someEmail';
    private const SOME_FORENAME = 'someForename';
    private const SOME_SURNAME = 'someSurname';
    private const SOME_PASSWORD = 'somePassword';

    /**
     * @var User
     */
    private $sut;

    protected function setUp(): void
    {
        $this->sut = new User();
    }

    public function testSetterAndGetter()
    {
        $roleEntity = new UserRole();
        $roleEntity->setCode(UserRole::ROLE_USER)
            ->setName(UserRole::ROLE_USER);
        $dateTime = new \DateTime();
        $this->sut->setActive(true)
            ->setEmail(self::SOME_EMAIL)
            ->setForename(self::SOME_FORENAME)
            ->setSurname(self::SOME_SURNAME)
            ->setLastLogin($dateTime)
            ->setPassword(self::SOME_PASSWORD);

        self::assertSame([UserRole::ROLE_USER], $this->sut->getRoles());
        $this->sut->addUserRole($roleEntity);
        $this->sut->removeUserRole($roleEntity);
        $this->sut->removeUserRole($roleEntity);
        $this->sut->addUserRole($roleEntity);

        self::assertNull($this->sut->getId());
        self::assertNull($this->sut->getSalt());
        self::assertTrue($this->sut->isActive());
        self::assertSame(self::SOME_EMAIL, $this->sut->getEmail());
        self::assertSame(self::SOME_EMAIL, $this->sut->getUsername());
        self::assertSame(self::SOME_FORENAME, $this->sut->getForename());
        self::assertSame(self::SOME_SURNAME, $this->sut->getSurname());
        self::assertInstanceOf(\DateTime::class, $this->sut->getLastLogin());
        self::assertSame($dateTime, $this->sut->getLastLogin());
        self::assertSame(self::SOME_PASSWORD, $this->sut->getPassword());
        self::assertSame([UserRole::ROLE_USER], $this->sut->getRoles());
        self::assertNotEmpty($this->sut->getRolesAsEntities());
        self::assertCount(1, $this->sut->getRolesAsEntities());
    }

    public function testSerializeAndUnserialize()
    {
        $roleEntity = new UserRole();
        $roleEntity->setCode(UserRole::ROLE_USER)
            ->setName(UserRole::ROLE_USER);
        $dateTime = new \DateTime();
        $this->sut->setActive(true)
            ->setEmail(self::SOME_EMAIL)
            ->setForename(self::SOME_FORENAME)
            ->setSurname(self::SOME_SURNAME)
            ->setLastLogin($dateTime)
            ->setPassword(self::SOME_PASSWORD)
            ->setRoles([$roleEntity]);

        $serializedUser = $this->sut->serialize();
        $this->sut->unserialize($serializedUser);

        self::assertNotNull($serializedUser);
        self::assertNull($this->sut->getId());
        self::assertNull($this->sut->getSalt());
        self::assertTrue($this->sut->isActive());
        self::assertSame(self::SOME_EMAIL, $this->sut->getEmail());
        self::assertSame(self::SOME_EMAIL, $this->sut->getUsername());
        self::assertSame(self::SOME_FORENAME, $this->sut->getForename());
        self::assertSame(self::SOME_SURNAME, $this->sut->getSurname());
        self::assertInstanceOf(\DateTime::class, $this->sut->getLastLogin());
        self::assertSame($dateTime, $this->sut->getLastLogin());
        self::assertSame(self::SOME_PASSWORD, $this->sut->getPassword());
        self::assertSame([UserRole::ROLE_USER], $this->sut->getRoles());
        self::assertNotEmpty($this->sut->getRolesAsEntities());
        self::assertCount(1, $this->sut->getRolesAsEntities());
    }
}
