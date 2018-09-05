<?php

namespace App\Controller\Api;

use App\Entity\Fax;
use App\Service\ResponseHandling\ResponseService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/api/twilio")
 */
class TwilioController extends AbstractController
{
    /**
     * @Route("/fax-receive")
     *
     * @param Request                $request
     * @param EntityManagerInterface $entityManager
     * @param ResponseService        $responseService
     *
     * @return Response
     */
    public function receiveFax(
        Request $request,
        EntityManagerInterface $entityManager,
        ResponseService $responseService
    ): Response {
        $fax = new Fax();
        $fax->setFaxDirection(Fax::DIRECTION_INBOUND)
            ->setToPhoneNumber($request->request->get('To'))
            ->setFromPhoneNumber($request->request->get('From'))
            ->setPagesCount($request->request->get('NumPages'))
            ->setRemoteStationId($request->request->get('RemoteStationId'))
            ->setMediaUrl($request->request->get('MediaUrl'))
            ->setFaxId($request->request->get('FaxSid'))
            ->setFaxStatus($request->request->get('Status'));

        $entityManager->persist($fax);
        $entityManager->flush();

        return $responseService->createJsonSuccessResponse();
    }

    /**
     * @Route("/get-file-once/{fileUniqueId}")
     *
     * @param string $fileUniqueId
     *
     * @return Response
     */
    public function getOneTimeFile(string $fileUniqueId): Response
    {
        $filePathOneTimeRequest = $this->getParameter('fax_onetime_access_path');

        if (false === file_exists($filePathOneTimeRequest . $fileUniqueId)) {
            return new Response('Requested file was not found!', 404);
        }

        $fileToServe = file_get_contents($filePathOneTimeRequest . $fileUniqueId);
        unlink($filePathOneTimeRequest . $fileUniqueId);

        return $this->file($fileToServe);
    }
}
