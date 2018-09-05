<?php

namespace App\Controller;

use App\Entity\Fax;
use App\Form\FaxSendType;
use App\Service\FaxService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\ResponseHeaderBag;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

/**
 * @Route("/fax")
 */
class FaxController extends AbstractController
{
    /**
     * @Route("/send")
     *
     * @param Request                $request
     * @param EntityManagerInterface $entityManager
     * @param FaxService             $faxService
     *
     * @return Response
     */
    public function send(
        Request $request,
        EntityManagerInterface $entityManager,
        FaxService $faxService
    ): Response {
        $sendFaxForm = $this->createForm(FaxSendType::class);
        $sendFaxForm->handleRequest($request);

        if ($sendFaxForm->isSubmitted() && $sendFaxForm->isValid()) {
            /** @var Fax $formData */
            $formData = $sendFaxForm->getData();

            /** @var UploadedFile $uploadedFile */
            $uploadedFile = $request->files->get('fax_send')['file'];
            $filePathOneTimeRequest = $this->getParameter('fax_onetime_access_path');
            $fileNameOneTimeRequest = str_replace('.', '', uniqid('', true));
            $filePath = $this->getParameter('fax_document_path');
            $filName = date('Y-m-d_H_i_s_') . microtime(true) . '.pdf';
            $fileSize = $uploadedFile->getSize();
            $uploadedFile->move(
                $filePath,
                $filName
            );

            if (!file_exists($filePathOneTimeRequest) && !mkdir($filePathOneTimeRequest, 0777, true)) {
                return new Response('Couldn\'t create onetime directory.', 400);
            }

            file_put_contents(
                $filePathOneTimeRequest . $fileNameOneTimeRequest,
                $filePath . $filName
            );

            $fax = $faxService->sendFax(
                $formData->getToPhoneNumber(),
                $this->generateUrl(
                    'app_api_twilio_getonetimefile',
                    [
                        'fileUniqueId' => $fileNameOneTimeRequest,
                    ],
                    UrlGeneratorInterface::ABSOLUTE_URL
                )
            );

            $fax->setLocalFilePath($filName)
                ->setLocalFileMime('application/pdf')
                ->setLocalFileSizeInBytes($fileSize);

            $entityManager->flush();

            return $this->render('fax/send_success.html.twig', [
                'fax' => $fax,
            ]);
        }

        return $this->render('fax/send.html.twig', [
            'form' => $sendFaxForm->createView(),
        ]);
    }

    /**
     * @Route("/file/{id}")
     *
     * @param Fax $fax
     *
     * @return Response
     */
    public function getMediaFile(Fax $fax): Response
    {
        $filePath = $this->getParameter('fax_document_path');
        $fullFilePath = $filePath . $fax->getLocalFilePath();

        if (null === $fax->getLocalFilePath() || !file_exists($fullFilePath)) {
            return new Response('File Not Found!', 404);
        }

        return $this->file($fullFilePath, null, ResponseHeaderBag::DISPOSITION_INLINE);
    }
}
