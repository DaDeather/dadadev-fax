<?php

namespace App\Tests\Functional\Service\ResponseHandling;

use App\Service\ResponseHandling\ResponseService;
use App\Service\ResponseHandling\ResponseStructure;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Response;

class ResponseServiceTest extends WebTestCase
{
    private const SOME_ERROR_MESSAGE = 'someErrorMessage';

    private const SOME_ERROR_CODE = 1530709351687;

    private const SOME_DATA = [
        'someDataKey' => 'someDataValue',
    ];

    public function testErrorResponseStructure(): void
    {
        self::bootKernel();

        /** @var ResponseService $responseService */
        $responseService = static::$container->get(ResponseService::class);

        $response = $responseService->createJsonErrorResponse(
            self::SOME_ERROR_MESSAGE,
            self::SOME_ERROR_CODE,
            self::SOME_DATA,
            404
        );

        self::assertInstanceOf(Response::class, $response);
        self::assertJson($response->getContent());
        self::assertSame(404, $response->getStatusCode());

        $decodedJson = json_decode($response->getContent(), true);
        self::assertSame(ResponseStructure::STATUS_ERROR, $decodedJson['status']);
        self::assertSame(self::SOME_ERROR_MESSAGE, $decodedJson['errorMessage']);
        self::assertSame(self::SOME_ERROR_CODE, $decodedJson['errorCode']);
        self::assertSame(self::SOME_DATA, $decodedJson['data']);
        self::assertNotNull($decodedJson['utcTimestamp']);
    }

    public function testJsonResponseStructure(): void
    {
        self::bootKernel();

        /** @var ResponseService $responseService */
        $responseService = static::$container->get(ResponseService::class);

        $response = $responseService->createJsonSuccessResponse(
            self::SOME_DATA,
            200
        );

        self::assertInstanceOf(Response::class, $response);
        self::assertJson($response->getContent());
        self::assertSame(200, $response->getStatusCode());

        $decodedJson = json_decode($response->getContent(), true);
        self::assertSame(ResponseStructure::STATUS_OK, $decodedJson['status']);
        self::assertNull($decodedJson['errorMessage']);
        self::assertNull($decodedJson['errorCode']);
        self::assertSame(self::SOME_DATA, $decodedJson['data']);
        self::assertNotNull($decodedJson['utcTimestamp']);
    }

    public function testErrorResponseStructureWithDebug(): void
    {
        self::bootKernel();

        /** @var ResponseService $responseService */
        $responseService = static::$container->get(ResponseService::class);
        $responseService->setIsDebug(true);

        $response = $responseService->createJsonErrorResponse(
            self::SOME_ERROR_MESSAGE,
            self::SOME_ERROR_CODE,
            self::SOME_DATA,
            404
        );

        self::assertInstanceOf(Response::class, $response);
        self::assertJson($response->getContent());
        self::assertSame(404, $response->getStatusCode());

        $decodedJson = json_decode($response->getContent(), true);
        self::assertSame(ResponseStructure::STATUS_ERROR, $decodedJson['status']);
        self::assertSame(self::SOME_ERROR_MESSAGE, $decodedJson['errorMessage']);
        self::assertSame(self::SOME_ERROR_CODE, $decodedJson['errorCode']);
        self::assertSame(self::SOME_DATA, $decodedJson['data']);
        self::assertNotNull($decodedJson['utcTimestamp']);
        self::assertNotNull($decodedJson['executionDurationInMs']);
        self::assertNotNull($decodedJson['memoryUsagePeakInMb']);
    }

    public function testJsonResponseStructureWithDebug(): void
    {
        self::bootKernel();

        /** @var ResponseService $responseService */
        $responseService = static::$container->get(ResponseService::class);
        $responseService->setIsDebug(true);

        $response = $responseService->createJsonSuccessResponse(
            self::SOME_DATA,
            200
        );

        self::assertInstanceOf(Response::class, $response);
        self::assertJson($response->getContent());
        self::assertSame(200, $response->getStatusCode());

        $decodedJson = json_decode($response->getContent(), true);
        self::assertSame(ResponseStructure::STATUS_OK, $decodedJson['status']);
        self::assertNull($decodedJson['errorMessage']);
        self::assertNull($decodedJson['errorCode']);
        self::assertSame(self::SOME_DATA, $decodedJson['data']);
        self::assertNotNull($decodedJson['utcTimestamp']);
        self::assertNotNull($decodedJson['executionDurationInMs']);
        self::assertNotNull($decodedJson['memoryUsagePeakInMb']);
    }
}
