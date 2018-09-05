<?php

namespace App\Tests\Unit\EventListener\Handler\Authentication;

use App\EventListener\Handler\Authentication\LogoutSuccessHandler;
use App\Service\ResponseHandling\ResponseService;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Http\HttpUtils;

class LogoutSuccessHandlerTest extends TestCase
{
    /** @var LogoutSuccessHandler */
    private $sut;

    /** @var HttpUtils|\PHPUnit_Framework_MockObject_MockObject */
    private $httpUtilsMock;

    /** @var ResponseService|\PHPUnit_Framework_MockObject_MockObject */
    private $responseServiceMock;

    protected function setUp()
    {
        $this->httpUtilsMock = $this->createMock(HttpUtils::class);
        $this->responseServiceMock = $this->createMock(ResponseService::class);

        $this->sut = new LogoutSuccessHandler($this->httpUtilsMock, $this->responseServiceMock);
    }

    public function testOnAuthenticationSuccessWithAsXmlHttpRequest(): void
    {
        /** @var Request|\PHPUnit_Framework_MockObject_MockObject $requestMock */
        $requestMock = $this->createMock(Request::class);
        $requestMock->method('isXmlHttpRequest')->willReturn(true);

        $response = $this->sut->onLogoutSuccess($requestMock);

        self::assertInstanceOf(Response::class, $response);
    }

    public function testOnAuthenticationFailureWithoutXmlHttpRequest(): void
    {
        /** @var Request|\PHPUnit_Framework_MockObject_MockObject $requestMock */
        $requestMock = $this->createMock(Request::class);
        $requestMock->method('isXmlHttpRequest')->willReturn(false);

        $response = $this->sut->onLogoutSuccess($requestMock);

        self::assertNull($response);
    }
}
