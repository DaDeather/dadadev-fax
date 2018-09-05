<?php

namespace App\Tests\Functional\Controller\Api;

use App\Entity\Fax;
use App\Repository\FaxRepository;
use App\Tests\Functional\AbstractControllerBaseWebTestCase;

class TwilioControllerTest extends AbstractControllerBaseWebTestCase
{
    private const BASE_ADDRESS = '/api/twilio';

    public function testReceiveFax(): void
    {
        $this->loadTestFixtures();

        $client = $this->getClient();
        $client->request(
            'POST',
            self::BASE_ADDRESS . '/fax-receive',
            [
                'To' => '+49987654321',
                'From' => '+49123456789',
                'NumPages' => 2,
                'RemoteStationId' => 'someRemoteStationId',
                'MediaUrl' => 'someMediaUrl',
                'FaxSid' => 'someFaxSid',
                'Status' => Fax::STATUS_RECEIVED,
            ]
        );

        $responseData = json_decode($client->getResponse()->getContent(), true);
        $this->assertJsonSuccessResponse($responseData);

        $faxRepository = self::$container->get(FaxRepository::class);
        $fax = $faxRepository->find(1);

        self::assertNotNull($fax);
        self::assertSame('+49987654321', $fax->getToPhoneNumber());
        self::assertSame('+49123456789', $fax->getFromPhoneNumber());
        self::assertSame(2, $fax->getPagesCount());
        self::assertSame('someRemoteStationId', $fax->getRemoteStationId());
        self::assertSame('someMediaUrl', $fax->getMediaUrl());
        self::assertSame('someFaxSid', $fax->getFaxId());
        self::assertSame(Fax::STATUS_RECEIVED, $fax->getFaxStatus());
    }

    public function testGetOneTimeFileFail(): void
    {
        $this->loadTestFixtures();

        $client = $this->getClient();
        $client->request(
            'GET',
            self::BASE_ADDRESS . '/get-file-once/someFileId'
        );

        self::assertSame('Requested file was not found!', $client->getResponse()->getContent());
        self::assertSame(404, $client->getResponse()->getStatusCode());
    }

    public function testGetOneTimeFileSuccess(): void
    {
        $this->loadTestFixtures();
        $this->prepareFileToTest();

        $client = $this->getClient();
        $client->request(
            'GET',
            self::BASE_ADDRESS . '/get-file-once/someFileId'
        );

        self::assertSame(200, $client->getResponse()->getStatusCode());
    }

    private function prepareFileToTest(): void
    {
        self::bootKernel();
        $faxDocumentPath = self::$container->getParameter('fax_document_path');
        $oneTimeAccessDir = self::$container->getParameter('fax_onetime_access_path');

        if (!file_exists($faxDocumentPath)) {
            mkdir($faxDocumentPath, 0777, true);
        }
        if (!file_exists($oneTimeAccessDir)) {
            mkdir($oneTimeAccessDir, 0777, true);
        }

        copy(__DIR__ . '/Data/someFile.pdf', $faxDocumentPath . 'someFile.pdf');
        file_put_contents($oneTimeAccessDir . 'someFileId', $faxDocumentPath . 'someFile.pdf');
    }
}
