<?php

namespace App\Tests\Unit\EventListener;

use App\EventListener\JsonToRequestListener;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\ParameterBag;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Event\GetResponseEvent;

class JsonToRequestListenerTest extends TestCase
{
    /**
     * @var JsonToRequestListener
     */
    private $sut;

    private const SOME_CONTENT_TYPE = 'json';
    private const SOME_JSON_DATA = '{"test":"data","data":{"moreData":"more","leet":1337}}';
    private const SOME_INVALID_JSON = 'someInvalidJsonData';
    private const SOME_DECODED_JSON = [
        'test' => 'data',
        'data' => [
            'moreData' => 'more',
            'leet' => 1337,
        ],
    ];

    protected function setUp()
    {
        $this->sut = new JsonToRequestListener();
    }

    public function testOnKernelRequestWithValidJson(): void
    {
        /** @var GetResponseEvent|\PHPUnit_Framework_MockObject_MockObject $getResponseEventMock */
        $getResponseEventMock = $this->createMock(GetResponseEvent::class);
        $getResponseEventMock->method('isMasterRequest')->willReturn(true);

        /** @var Request|\PHPUnit_Framework_MockObject_MockObject $requestMock */
        $requestMock = $this->createMock(Request::class);

        $getResponseEventMock->method('getRequest')->willReturn($requestMock);

        $requestMock->method('getContentType')->willReturn(self::SOME_CONTENT_TYPE);
        $requestMock->method('getContent')->willReturn(self::SOME_JSON_DATA);

        /** @var ParameterBag|\PHPUnit_Framework_MockObject_MockObject $parameterBagMock */
        $parameterBagMock = $this->createMock(ParameterBag::class);
        $parameterBagMock->method('replace')->with(self::SOME_DECODED_JSON);
        $requestMock->request = $parameterBagMock;

        $this->sut->onKernelRequest($getResponseEventMock);

        self::assertTrue(true);
    }

    public function testOnKernelRequestWithoutAnyData(): void
    {
        /** @var GetResponseEvent|\PHPUnit_Framework_MockObject_MockObject $getResponseEventMock */
        $getResponseEventMock = $this->createMock(GetResponseEvent::class);
        $getResponseEventMock->method('isMasterRequest')->willReturn(true);

        /** @var Request|\PHPUnit_Framework_MockObject_MockObject $requestMock */
        $requestMock = $this->createMock(Request::class);

        $getResponseEventMock->method('getRequest')->willReturn($requestMock);

        $requestMock->method('getContentType')->willReturn(self::SOME_CONTENT_TYPE);

        $this->sut->onKernelRequest($getResponseEventMock);

        self::assertTrue(true);
    }

    public function testOnKernelRequestWithoutValidJson(): void
    {
        /** @var GetResponseEvent|\PHPUnit_Framework_MockObject_MockObject $getResponseEventMock */
        $getResponseEventMock = $this->createMock(GetResponseEvent::class);
        $getResponseEventMock->method('isMasterRequest')->willReturn(true);

        /** @var Request|\PHPUnit_Framework_MockObject_MockObject $requestMock */
        $requestMock = $this->createMock(Request::class);

        $getResponseEventMock->method('getRequest')->willReturn($requestMock);

        $requestMock->method('getContentType')->willReturn(self::SOME_CONTENT_TYPE);
        $requestMock->method('getContent')->willReturn(self::SOME_INVALID_JSON);

        $this->sut->onKernelRequest($getResponseEventMock);

        self::assertTrue(true);
    }

    public function testOnKernelRequestWithoutMasterRequest(): void
    {
        /** @var GetResponseEvent|\PHPUnit_Framework_MockObject_MockObject $getResponseEventMock */
        $getResponseEventMock = $this->createMock(GetResponseEvent::class);
        $getResponseEventMock->method('isMasterRequest')->willReturn(false);

        $this->sut->onKernelRequest($getResponseEventMock);

        self::assertTrue(true);
    }
}
