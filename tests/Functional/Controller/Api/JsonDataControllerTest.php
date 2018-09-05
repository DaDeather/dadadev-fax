<?php

namespace App\Tests\Functional\Controller\Api;

use App\Entity\Fax;
use App\Tests\Functional\AbstractControllerBaseWebTestCase;

class JsonDataControllerTest extends AbstractControllerBaseWebTestCase
{
    private const BASE_ADDRESS = '/api/json';

    public function testReceivedFaxes(): void
    {
        $this->loadTestFixtures([
            __DIR__ . '/../../Fixtures/Fax.yml',
        ]);

        $client = $this->getClient();
        $client->request(
            'GET',
            self::BASE_ADDRESS . '/received-faxes'
        );

        $responseData = json_decode($client->getResponse()->getContent(), true);

        self::assertNotNull($responseData);
        self::assertNotFalse($responseData);

        $this->assertJsonSuccessResponse($responseData);

        self::assertNotNull($responseData['data'][0]);
        $firstFax = $responseData['data'][0];
        self::assertSame(Fax::DIRECTION_INBOUND, $firstFax['faxDirection']);
        self::assertNotNull($firstFax['faxId']);
        self::assertNotNull($firstFax['created']);
    }

    public function testSentFaxes(): void
    {
        $this->loadTestFixtures([
            __DIR__ . '/../../Fixtures/Fax.yml',
        ]);

        $client = $this->getClient();
        $client->request(
            'GET',
            self::BASE_ADDRESS . '/sent-faxes'
        );

        $responseData = json_decode($client->getResponse()->getContent(), true);
        $this->assertJsonSuccessResponse($responseData);

        self::assertNotNull($responseData);
        self::assertNotFalse($responseData);

        self::assertNotNull($responseData['data'][0]);
        $firstFax = $responseData['data'][0];
        self::assertSame(Fax::DIRECTION_OUTBOUND, $firstFax['faxDirection']);
        self::assertNotNull($firstFax['faxId']);
        self::assertNotNull($firstFax['created']);
    }
}
