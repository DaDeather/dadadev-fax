<?php

namespace App\EventListener\Handler\Authentication;

use App\Service\ResponseHandling\ResponseService;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Http\HttpUtils;
use Symfony\Component\Security\Http\Logout\DefaultLogoutSuccessHandler;

class LogoutSuccessHandler extends DefaultLogoutSuccessHandler
{
    /** @var ResponseService */
    private $responseService;

    public function __construct(
        HttpUtils $httpUtils,
        ResponseService $responseService
    ) {
        parent::__construct($httpUtils, '/');

        $this->responseService = $responseService;
    }

    public function onLogoutSuccess(Request $request)
    {
        if (true === $request->isXmlHttpRequest()) {
            return $this->responseService->createJsonSuccessResponse();
        }

        return parent::onLogoutSuccess($request);
    }
}
