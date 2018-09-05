<?php

namespace App\Tests\Unit\Service\ResponseHandling;

use App\Service\ResponseHandling\ResponseStructure;
use PHPUnit\Framework\TestCase;

class ResponseStructureTest extends TestCase
{
    /**
     * @var ResponseStructure
     */
    private $sut;

    protected function setUp(): void
    {
        $this->sut = new ResponseStructure();
    }

    public function testStatus(): void
    {
        $this->sut->setStatus(ResponseStructure::STATUS_OK);
        self::assertSame(ResponseStructure::STATUS_OK, $this->sut->getStatus());
        $this->sut->setStatus(ResponseStructure::STATUS_ERROR);
        self::assertSame(ResponseStructure::STATUS_ERROR, $this->sut->getStatus());
        $this->sut->setStatus(ResponseStructure::STATUS_UNDEFINED);
        self::assertSame(ResponseStructure::STATUS_UNDEFINED, $this->sut->getStatus());
    }

    public function testErrorCode(): void
    {
        $this->sut->setErrorCode(1);
        self::assertSame(1, $this->sut->getErrorCode());
        $this->sut->setErrorCode(2);
        self::assertSame(2, $this->sut->getErrorCode());
        $this->sut->setErrorCode(3);
        self::assertSame(3, $this->sut->getErrorCode());
    }

    public function testErrorMessage(): void
    {
        $this->sut->setErrorMessage('someErrorMessage1');
        self::assertSame('someErrorMessage1', $this->sut->getErrorMessage());
        $this->sut->setErrorMessage('someErrorMessage2');
        self::assertSame('someErrorMessage2', $this->sut->getErrorMessage());
        $this->sut->setErrorMessage('someErrorMessage3');
        self::assertSame('someErrorMessage3', $this->sut->getErrorMessage());
    }

    public function testData(): void
    {
        $this->sut->setData([]);
        self::assertSame([], $this->sut->getData());
        $this->sut->setData(['someDataVal']);
        self::assertSame(['someDataVal'], $this->sut->getData());
        $this->sut->setData(['someDataKey' => 'someDataValue']);
        self::assertSame(['someDataKey' => 'someDataValue'], $this->sut->getData());
    }
}
