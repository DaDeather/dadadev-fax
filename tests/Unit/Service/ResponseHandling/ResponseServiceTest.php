<?php

namespace App\Tests\Unit\Service\ResponseHandling;

use App\Service\ResponseHandling\ResponseService;
use JMS\Serializer\SerializerInterface;
use PHPUnit\Framework\TestCase;

class ResponseServiceTest extends TestCase
{
    /**
     * @var ResponseService
     */
    private $sut;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject|SerializerInterface
     */
    private $serializerMock;

    protected function setUp(): void
    {
        $this->serializerMock = $this->getMockBuilder(SerializerInterface::class)
            ->setMethods([
                'serialize',
                'deserialize',
            ])
            ->getMock();

        $this->sut = new ResponseService($this->serializerMock);
    }

    public function testIsDebug(): void
    {
        $this->sut->setIsDebug(true);
        self::assertTrue($this->sut->isDebug());
        $this->sut->setIsDebug(false);
        self::assertFalse($this->sut->isDebug());
    }

    public function testVersion(): void
    {
        $this->sut->setVersion(1);
        self::assertSame(1, $this->sut->getVersion());
        $this->sut->setVersion(2);
        self::assertSame(2, $this->sut->getVersion());
        $this->sut->setVersion(0);
        self::assertSame(0, $this->sut->getVersion());
    }

    public function testGroups(): void
    {
        $this->sut->setGroups([ResponseService::GROUP_DEBUG]);
        $this->sut->addGroup(ResponseService::GROUP_DEBUG);
        $this->sut->addGroup(ResponseService::GROUP_DEBUG);
        $this->sut->addGroup(ResponseService::GROUP_DEFAULT);
        $this->sut->removeGroup(ResponseService::GROUP_DEFAULT);
        self::assertTrue(true);
    }
}
