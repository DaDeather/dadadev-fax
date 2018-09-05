<?php

namespace App\Controller;

use App\Entity\Fax;
use App\Repository\FaxRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/")
 */
class DashboardController extends AbstractController
{
    /**
     * @Route("/")
     *
     * @param FaxRepository $faxRepository
     *
     * @return Response
     */
    public function dashboard(FaxRepository $faxRepository): Response
    {
        $sentFaxes = $faxRepository->findAllFax(Fax::DIRECTION_OUTBOUND);
        $receivedFaxes = $faxRepository->findAllFax(Fax::DIRECTION_INBOUND);

        $twilioPhoneNumber = getenv('TWILIO_ACCOUNT_SENDING_NO');

        return $this->render('dashboard/index.html.twig', [
            'twilioPhoneNumber' => $twilioPhoneNumber,
            'sentFaxes' => $sentFaxes,
            'receivedFaxes' => $receivedFaxes,
            'FINISHED_RECEIVE_FAILED_STATE_COLLECTION' => Fax::FINISHED_RECEIVE_FAILED_STATE_COLLECTION,
        ]);
    }
}
