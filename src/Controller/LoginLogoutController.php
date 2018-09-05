<?php

namespace App\Controller;

use App\EventListener\Handler\Authentication\LoginFailureHandler;
use App\EventListener\Handler\Authentication\LoginSuccessHandler;
use App\EventListener\Handler\Authentication\LogoutSuccessHandler;
use App\Service\ResponseHandling\ResponseService;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

/**
 * @Route("/")
 */
class LoginLogoutController extends Controller
{
    /**
     * This route should be called with 'username' and 'password' as GET query string or POST form encoded.
     * The handling of login is done in:.
     *
     * @see LoginSuccessHandler
     * @see LoginFailureHandler
     *
     * @Route("/login")
     *
     * @param AuthenticationUtils $authenticationUtils
     *
     * @return Response
     */
    public function login(AuthenticationUtils $authenticationUtils): Response
    {
        // get the login error if there is one
        $error = $authenticationUtils->getLastAuthenticationError();

        $lastUsername = $authenticationUtils->getLastUsername();

        return $this->render('security/login.html.twig', [
            'last_username' => $lastUsername,
            'error' => $error,
        ]);
    }

    /**
     * Logout is handled in:.
     *
     * @see LogoutSuccessHandler
     *
     * @codeCoverageIgnore
     *
     * @Route("/logout")
     *
     * @param ResponseService $responseService
     *
     * @return Response
     */
    public function logout(ResponseService $responseService): Response
    {
        return $responseService->createJsonSuccessResponse();
    }
}
