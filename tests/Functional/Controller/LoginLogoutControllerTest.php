<?php

namespace App\Tests\Functional\Controller;

use App\Tests\Functional\AbstractControllerBaseWebTestCase;

class LoginLogoutControllerTest extends AbstractControllerBaseWebTestCase
{
    private const BASE_ADDRESS = '/login';

    public function testLoginPage(): void
    {
        $this->loadTestFixtures();

        $client = $this->getClient(null, null);

        $client->request(
            'GET',
            self::BASE_ADDRESS
        );

        self::assertSame(200, $client->getResponse()->getStatusCode());
    }

    public function testLoginSend(): void
    {
        $this->loadTestFixtures();

        $client = $this->getClient(null, null);

        $client->request(
            'POST',
            self::BASE_ADDRESS,
            [
                'username' => 'oezguen.turan@dadadev.com',
                'password' => 'test',
            ]
        );

        self::assertSame(200, $client->getResponse()->getStatusCode());
    }
}
