<?php

namespace App\Tests\Unit\EventListener\Handler\Authentication;

use App\Entity\User;
use App\EventListener\Handler\Authentication\LoginSuccessHandler;
use App\Service\ResponseHandling\ResponseService;
use Doctrine\ORM\EntityManagerInterface;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Http\HttpUtils;

class LoginSuccessHandlerTest extends TestCase
{
    /** @var LoginSuccessHandler */
    private $sut;

    /** @var HttpUtils|\PHPUnit_Framework_MockObject_MockObject */
    private $httpUtilsMock;

    /** @var ResponseService|\PHPUnit_Framework_MockObject_MockObject */
    private $responseServiceMock;

    /** @var EntityManagerInterface|\PHPUnit_Framework_MockObject_MockObject */
    private $entityManagerMock;

    protected function setUp()
    {
        $this->entityManagerMock = $this->getMockBuilder(EntityManagerInterface::class)->getMock();
        $this->httpUtilsMock = $this->createMock(HttpUtils::class);
        $this->responseServiceMock = $this->createMock(ResponseService::class);

        $this->sut = new LoginSuccessHandler($this->httpUtilsMock, $this->entityManagerMock, $this->responseServiceMock);
    }

    public function testOnAuthenticationSuccessWithAsXmlHttpRequest(): void
    {
        /** @var Request|\PHPUnit_Framework_MockObject_MockObject $requestMock */
        $requestMock = $this->createMock(Request::class);
        $requestMock->method('isXmlHttpRequest')->willReturn(true);

        /** @var User|\PHPUnit_Framework_MockObject_MockObject $userMock */
        $userMock = $this->createMock(User::class);

        /** @var TokenInterface|\PHPUnit_Framework_MockObject_MockObject $tokenInterfaceMock */
        $tokenInterfaceMock = $this->getMockBuilder(TokenInterface::class)->getMock();
        $tokenInterfaceMock->expects(self::once())->method('getUser')->willReturn($userMock);

        $this->responseServiceMock->expects(self::once())
            ->method('createJsonSuccessResponse')
            ->with([
                'user' => $userMock,
            ]);

        $response = $this->sut->onAuthenticationSuccess($requestMock, $tokenInterfaceMock);

        self::assertInstanceOf(Response::class, $response);
    }

    public function testOnAuthenticationFailureWithoutXmlHttpRequest(): void
    {
        /** @var Request|\PHPUnit_Framework_MockObject_MockObject $requestMock */
        $requestMock = $this->createMock(Request::class);
        $requestMock->method('isXmlHttpRequest')->willReturn(false);

        /** @var User|\PHPUnit_Framework_MockObject_MockObject $userMock */
        $userMock = $this->createMock(User::class);

        /** @var TokenInterface|\PHPUnit_Framework_MockObject_MockObject $tokenInterfaceMock */
        $tokenInterfaceMock = $this->getMockBuilder(TokenInterface::class)->getMock();
        $tokenInterfaceMock->expects(self::once())->method('getUser')->willReturn($userMock);

        $response = $this->sut->onAuthenticationSuccess($requestMock, $tokenInterfaceMock);

        self::assertNull($response);
    }
}
