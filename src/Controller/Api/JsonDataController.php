<?php

namespace App\Controller\Api;

use App\Entity\Fax;
use App\Repository\FaxRepository;
use App\Service\ResponseHandling\ResponseService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/api/json")
 */
class JsonDataController extends AbstractController
{
    /**
     * @Route("/sent-faxes")
     *
     * @param FaxRepository   $faxRepository
     * @param ResponseService $responseService
     *
     * @return Response
     */
    public function sentFaxes(FaxRepository $faxRepository, ResponseService $responseService): Response
    {
        $sentFaxes = $faxRepository->findAllFax(Fax::DIRECTION_OUTBOUND);

        return $responseService->createJsonSuccessResponse($sentFaxes);
    }

    /**
     * @Route("/received-faxes")
     *
     * @param FaxRepository   $faxRepository
     * @param ResponseService $responseService
     *
     * @return Response
     */
    public function receivedFaxes(FaxRepository $faxRepository, ResponseService $responseService): Response
    {
        $receivedFaxes = $faxRepository->findAllFax(Fax::DIRECTION_INBOUND);

        return $responseService->createJsonSuccessResponse($receivedFaxes);
    }
}
