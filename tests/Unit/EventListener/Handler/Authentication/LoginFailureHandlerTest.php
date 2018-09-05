<?php

namespace App\Tests\Unit\EventListener\Handler\Authentication;

use App\EventListener\Handler\Authentication\LoginFailureHandler;
use App\Service\ResponseHandling\ResponseService;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\ParameterBag;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\HttpKernel\HttpKernelInterface;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Http\HttpUtils;

class LoginFailureHandlerTest extends TestCase
{
    private const SOME_EXCEPTION_MESSAGE = 'someExceptionMessage';

    /** @var LoginFailureHandler */
    private $sut;

    /** @var HttpUtils|\PHPUnit_Framework_MockObject_MockObject */
    private $httpUtilsMock;

    /** @var ResponseService|\PHPUnit_Framework_MockObject_MockObject */
    private $responseServiceMock;

    /** @var HttpKernelInterface|\PHPUnit_Framework_MockObject_MockObject */
    private $httpKernelMock;

    protected function setUp()
    {
        $this->httpKernelMock = $this->getMockBuilder(HttpKernelInterface::class)->getMock();
        $this->httpUtilsMock = $this->createMock(HttpUtils::class);
        $this->responseServiceMock = $this->createMock(ResponseService::class);

        $this->sut = new LoginFailureHandler($this->httpKernelMock, $this->httpUtilsMock, $this->responseServiceMock);
    }

    public function testOnAuthenticationFailureWithAsXmlHttpRequest(): void
    {
        /** @var Request|\PHPUnit_Framework_MockObject_MockObject $requestMock */
        $requestMock = $this->createMock(Request::class);
        $requestMock->method('isXmlHttpRequest')->willReturn(true);

        $authenticationExceptionMock = new AuthenticationException(self::SOME_EXCEPTION_MESSAGE);

        $this->responseServiceMock->expects(self::once())
            ->method('createJsonErrorResponse')
            ->with(self::SOME_EXCEPTION_MESSAGE, 1530647521668);

        $response = $this->sut->onAuthenticationFailure($requestMock, $authenticationExceptionMock);

        self::assertInstanceOf(Response::class, $response);
    }

    public function testOnAuthenticationFailureWithoutXmlHttpRequest(): void
    {
        $parameterBagMock = $this->createMock(ParameterBag::class);
        $sessionInterfaceMock = $this->getMockBuilder(SessionInterface::class)->getMock();

        /** @var Request|\PHPUnit_Framework_MockObject_MockObject $requestMock */
        $requestMock = $this->createMock(Request::class);
        $requestMock->method('isXmlHttpRequest')->willReturn(false);
        $requestMock->method('getSession')->willReturn($sessionInterfaceMock);
        $requestMock->attributes = $parameterBagMock;

        $authenticationExceptionMock = new AuthenticationException(self::SOME_EXCEPTION_MESSAGE);

        $response = $this->sut->onAuthenticationFailure($requestMock, $authenticationExceptionMock);

        self::assertNull($response);
    }
}
