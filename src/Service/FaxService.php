<?php

namespace App\Service;

use App\Entity\Fax;
use App\Repository\FaxRepository;
use Doctrine\ORM\EntityManagerInterface;
use Twilio\Exceptions\TwilioException;
use Twilio\Rest\Client;

class FaxService
{
    /** @var Client */
    private $twilioClient;

    /** @var EntityManagerInterface */
    private $entityManager;

    /** @var FaxRepository */
    private $faxRepository;

    /** @var string */
    private $sendingPhoneNumber;

    public function __construct(
        string $sendingPhoneNumber,
        Client $twilioClient,
        EntityManagerInterface $entityManager,
        FaxRepository $faxRepository
    ) {
        $this->sendingPhoneNumber = $sendingPhoneNumber;
        $this->twilioClient = $twilioClient;
        $this->entityManager = $entityManager;
        $this->faxRepository = $faxRepository;
    }

    /**
     * @param string $to
     * @param string $documentUrl
     *
     * @return Fax
     */
    public function sendFax(
        string $to,
        string $documentUrl
    ): Fax {
        $fax = new Fax();
        $fax->setFromPhoneNumber($this->sendingPhoneNumber)
            ->setToPhoneNumber($to)
            ->setFaxDirection(Fax::DIRECTION_OUTBOUND);

        $sendingFax = $this->twilioClient
            ->fax
            ->v1
            ->faxes
            ->create(
                $fax->getToPhoneNumber(),
                $documentUrl,
                [
                    'from' => $fax->getFromPhoneNumber(),
                ]
            );

        $fax->setFaxId($sendingFax->sid)
            ->setFaxStatus($sendingFax->status);

        $this->entityManager->persist($fax);
        $this->entityManager->flush();

        return $fax;
    }

    /**
     * @param string $faxId
     *
     * @return null|Fax
     */
    public function updateAndGetFax(
        string $faxId
    ): ?Fax {
        $fax = $this->faxRepository->findFaxByFaxId($faxId);

        if (null === $fax) {
            $fax = new Fax();
            $fax->setFaxId($faxId);
        }

        try {
            $sendingFax = $this->twilioClient
                ->fax
                ->v1
                ->faxes($fax->getFaxId())
                ->fetch()
            ;
        } catch (TwilioException $twilioException) {
            return null;
        }

        $fax->setFaxId($sendingFax->sid)
            ->setFaxDirection($sendingFax->direction)
            ->setFaxStatus($sendingFax->status)
            ->setFromPhoneNumber($sendingFax->from)
            ->setToPhoneNumber($sendingFax->to)
            ->setDuration($sendingFax->duration)
            ->setMediaUrl($sendingFax->mediaUrl)
            ->setMediaId($sendingFax->mediaSid)
            ->setPagesCount($sendingFax->numPages)
            ->setPrice($sendingFax->price)
            ->setPriceUnit($sendingFax->priceUnit);

        $this->entityManager->persist($fax);
        $this->entityManager->flush();

        return $fax;
    }
}
