<?php

namespace App\Tests\Unit\EventListener;

use App\EventListener\CorsListener;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\HeaderBag;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\ResponseHeaderBag;
use Symfony\Component\HttpKernel\Event\FilterResponseEvent;
use Symfony\Component\HttpKernel\Event\GetResponseEvent;

class CorsListenerTest extends TestCase
{
    private const SOME_ORIGIN = 'someOrigin';

    /**
     * @var CorsListener
     */
    private $sut;

    protected function setUp()
    {
        $this->sut = new CorsListener();
    }

    public function testOnKernelRequestNoMasterRequest(): void
    {
        /** @var GetResponseEvent|\PHPUnit_Framework_MockObject_MockObject $getResponseEventMock */
        $getResponseEventMock = $this->createMock(GetResponseEvent::class);
        $getResponseEventMock->method('isMasterRequest')->willReturn(false);

        $this->sut->onKernelRequest($getResponseEventMock);

        self::assertTrue(true);
    }

    public function testOnKernelRequestMethodOptions(): void
    {
        /** @var Request|\PHPUnit_Framework_MockObject_MockObject $requestMock */
        $requestMock = $this->createMock(Request::class);
        $requestMock->method('getRealMethod')->willReturn('OPTIONS');

        /** @var GetResponseEvent|\PHPUnit_Framework_MockObject_MockObject $getResponseEventMock */
        $getResponseEventMock = $this->createMock(GetResponseEvent::class);
        $getResponseEventMock->method('isMasterRequest')->willReturn(true);
        $getResponseEventMock->method('getRequest')->willReturn($requestMock);

        $this->sut->onKernelRequest($getResponseEventMock);

        self::assertTrue(true);
    }

    public function testOnKernelResponseNoMasterRequest(): void
    {
        /** @var FilterResponseEvent|\PHPUnit_Framework_MockObject_MockObject $getResponseEventMock */
        $getResponseEventMock = $this->createMock(FilterResponseEvent::class);
        $getResponseEventMock->method('isMasterRequest')->willReturn(false);

        $this->sut->onKernelResponse($getResponseEventMock);

        self::assertTrue(true);
    }

    public function testOnKernelResponseNoEmptyOrigin(): void
    {
        /** @var HeaderBag|\PHPUnit_Framework_MockObject_MockObject $requestHeaderBagMock */
        $requestHeaderBagMock = $this->createMock(HeaderBag::class);
        $requestHeaderBagMock->method('get')->willReturn(self::SOME_ORIGIN);
        /** @var ResponseHeaderBag|\PHPUnit_Framework_MockObject_MockObject $responseHeaderBagMock */
        $responseHeaderBagMock = $this->createMock(ResponseHeaderBag::class);

        /** @var Request|\PHPUnit_Framework_MockObject_MockObject $requestMock */
        $requestMock = $this->createMock(Request::class);
        $requestMock->method('getRealMethod')->willReturn('OPTIONS');
        $requestMock->headers = $requestHeaderBagMock;
        /** @var Request|\PHPUnit_Framework_MockObject_MockObject $responseMock */
        $responseMock = $this->createMock(Request::class);
        $responseMock->method('getRealMethod')->willReturn('OPTIONS');
        $responseMock->headers = $responseHeaderBagMock;

        /** @var FilterResponseEvent|\PHPUnit_Framework_MockObject_MockObject $getResponseEventMock */
        $getResponseEventMock = $this->createMock(FilterResponseEvent::class);
        $getResponseEventMock->method('isMasterRequest')->willReturn(true);
        $getResponseEventMock->method('getResponse')->willReturn($responseMock);
        $getResponseEventMock->method('getRequest')->willReturn($requestMock);

        $this->sut->onKernelResponse($getResponseEventMock);

        self::assertTrue(true);
    }

    public function testGetSubscribedEvents(): void
    {
        CorsListener::getSubscribedEvents();

        self:self::assertTrue(true);
    }
}
