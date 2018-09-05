<?php

namespace App\Tests\Functional;

use App\Service\ResponseHandling\ResponseStructure;
use Doctrine\Common\DataFixtures\Purger\ORMPurger;
use Liip\FunctionalTestBundle\Test\WebTestCase;
use Symfony\Bundle\FrameworkBundle\Client;
use Symfony\Component\DomCrawler\Crawler;

abstract class AbstractControllerBaseWebTestCase extends WebTestCase
{
    /** @var bool */
    private $loggedIn = false;

    /** @var null|Client */
    private $client = null;

    protected function loadTestFixtures(array $additionalFixtures = [])
    {
        $this->loadFixtureFiles(
            array_merge(
                [
                    __DIR__ . '/Fixtures/Users.yml',
                    __DIR__ . '/Fixtures/UserRoles.yml',
                ],
                $additionalFixtures
            ),
            false,
            null,
            'doctrine',
            ORMPurger::PURGE_MODE_TRUNCATE
        );
    }

    /**
     * @param null|string $loginUsername
     * @param null|string $loginPassword
     *
     * @return Client
     */
    protected function getClient(
        ?string $loginUsername = 'oezguen.turan@dadadev.com',
        ?string $loginPassword = 'test'
    ): Client {
        $loginData = false;
        if ($loginUsername && $loginPassword) {
            $loginData = [
                'username' => $loginUsername,
                'password' => $loginPassword,
            ];
        }
        $client = $this->makeClient($loginData);
        $client->followRedirects(true);

        $this->loggedIn = true;
        $this->client = $client;

        return $this->client;
    }

    /**
     * @param string $method
     * @param string $url
     * @param string $content
     *
     * @return Crawler
     */
    protected function sendJsonRequest(string $method, string $url, string $content = null): Crawler
    {
        $client = $this->getClient();
        $crawler = $client->request(
            $method,
            $url,
            [],
            [],
            ['CONTENT_TYPE' => 'application/json'],
            $content
        );

        return $crawler;
    }

    /**
     * @param array  $jsonResponseData
     * @param string $message
     * @param int    $code
     */
    protected function assertJsonErrorResponse(array $jsonResponseData, string $message, int $code): void
    {
        static::assertSame(ResponseStructure::STATUS_ERROR, $jsonResponseData['status']);
        static::assertSame($message, $jsonResponseData['errorMessage']);
        static::assertSame($code, $jsonResponseData['errorCode']);
        static::assertNotNull($jsonResponseData['utcTimestamp']);
    }

    /**
     * @param array $jsonResponseData
     */
    protected function assertJsonSuccessResponse(array $jsonResponseData): void
    {
        if (null === $this->client) {
            throw new \RuntimeException('Client is not initialized!', 1534343067141);
        }

        static::assertSame(200, $this->client->getResponse()->getStatusCode());
        static::assertSame(ResponseStructure::STATUS_OK, $jsonResponseData['status']);
        static::assertNull($jsonResponseData['errorMessage']);
        static::assertNull($jsonResponseData['errorCode']);
        static::assertNotNull($jsonResponseData['utcTimestamp']);
    }
}
