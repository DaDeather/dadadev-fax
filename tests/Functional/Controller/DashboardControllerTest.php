<?php

namespace App\Tests\Functional\Controller;

use App\Tests\Functional\AbstractControllerBaseWebTestCase;

class DashboardControllerTest extends AbstractControllerBaseWebTestCase
{
    private const BASE_ADDRESS = '/';

    public function testCallingDashboard(): void
    {
        $this->loadTestFixtures();

        $client = $this->getClient();

        $client->request(
            'GET',
            self::BASE_ADDRESS
        );

        self::assertSame(200, $client->getResponse()->getStatusCode());
    }

    public function testNavigationSendFaxExists(): void
    {
        $this->loadTestFixtures();

        $client = $this->getClient();

        $crawler = $client->request(
            'GET',
            self::BASE_ADDRESS
        );

        $link = $crawler->filter('#send-fax-link')->link();

        self::assertContains('/fax/send', $link->getUri());
        self::assertSame(200, $client->getResponse()->getStatusCode());
    }

    public function testNavigationCheckFaxTables(): void
    {
        $this->loadTestFixtures([
            __DIR__ . '/../Fixtures/Fax.yml',
        ]);

        $client = $this->getClient();

        $crawler = $client->request(
            'GET',
            self::BASE_ADDRESS
        );

        $sentFaxesCount = $crawler->filter('#sent-faxes tbody tr th')->count();
        $receivedFaxesCount = $crawler->filter('#received-faxes tbody tr th')->count();

        self::assertSame(10, ($sentFaxesCount + $receivedFaxesCount));
        self::assertSame(200, $client->getResponse()->getStatusCode());
    }
}
