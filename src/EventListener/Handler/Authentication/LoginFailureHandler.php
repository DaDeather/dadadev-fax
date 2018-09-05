<?php

namespace App\EventListener\Handler\Authentication;

use App\Service\ResponseHandling\ResponseService;
use Symfony\Component\HttpKernel\HttpKernelInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Http\Authentication\DefaultAuthenticationFailureHandler;
use Symfony\Component\Security\Http\HttpUtils;
use Symfony\Component\Security\Core\Exception\AuthenticationException;

class LoginFailureHandler extends DefaultAuthenticationFailureHandler
{
    /** @var ResponseService */
    private $responseService;

    public function __construct(HttpKernelInterface $httpKernel, HttpUtils $httpUtils, ResponseService $responseService)
    {
        parent::__construct($httpKernel, $httpUtils, []);

        $this->responseService = $responseService;
    }

    public function onAuthenticationFailure(Request $request, AuthenticationException $exception)
    {
        if (true === $request->isXmlHttpRequest()) {
            return $this->responseService->createJsonErrorResponse($exception->getMessage(), 1530647521668);
        }

        return parent::onAuthenticationFailure($request, $exception);
    }
}
