<?php

namespace App\Tests\Functional\Controller\Api;

use App\Tests\Functional\AbstractControllerBaseWebTestCase;

class TwilioTwimlControllerTest extends AbstractControllerBaseWebTestCase
{
    private const BASE_ADDRESS = '/api/twilio/twiml';
    private const EXPECTED_TWIML_FAX_RECEIVE = <<<'FAX_RECEIVE'
<?xml version="1.0" encoding="UTF-8"?>
<Response><Receive action="http://localhost/api/twilio/fax-receive" pageSize="a4"/></Response>

FAX_RECEIVE;

    public function testReceiveFaxTwimlGeneration(): void
    {
        $this->loadTestFixtures();

        $client = $this->getClient();
        $client->request(
            'GET',
            self::BASE_ADDRESS . '/fax-receive'
        );

        $responseData = $client->getResponse()->getContent();

        /** @var \SimpleXMLElement $xml */
        $xml = simplexml_load_string($responseData);

        self::assertSame(200, $client->getResponse()->getStatusCode());
        self::assertSame(self::EXPECTED_TWIML_FAX_RECEIVE, $responseData);
        self::assertNotNull($xml);
        self::assertStringStartsWith('<?xml version="1.0" encoding="UTF-8"?>', $responseData);
        self::assertSame('http://localhost/api/twilio/fax-receive', (string) $xml->Receive['action']);
        self::assertSame('a4', (string) $xml->Receive['pageSize']);
    }
}
