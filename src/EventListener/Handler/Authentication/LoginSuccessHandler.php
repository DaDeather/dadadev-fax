<?php

namespace App\EventListener\Handler\Authentication;

use App\Entity\User;
use App\Service\ResponseHandling\ResponseService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Http\Authentication\DefaultAuthenticationSuccessHandler;
use Symfony\Component\Security\Http\HttpUtils;

class LoginSuccessHandler extends DefaultAuthenticationSuccessHandler
{
    /** @var EntityManagerInterface */
    protected $entityManager;

    /** @var ResponseService */
    private $responseService;

    public function __construct(
        HttpUtils $httpUtils,
        EntityManagerInterface $entityManager,
        ResponseService $responseService
    ) {
        parent::__construct($httpUtils, []);

        $this->entityManager = $entityManager;
        $this->responseService = $responseService;
    }

    public function onAuthenticationSuccess(Request $request, TokenInterface $token)
    {
        /* @var $currentUser User */
        $currentUser = $token->getUser();
        $currentUser->setLastLogin(new \DateTime());
        $this->entityManager->flush();

        if (true === $request->isXmlHttpRequest()) {
            return $this->responseService->createJsonSuccessResponse([
                'user' => $currentUser,
            ]);
        }

        return parent::onAuthenticationSuccess($request, $token);
    }
}
