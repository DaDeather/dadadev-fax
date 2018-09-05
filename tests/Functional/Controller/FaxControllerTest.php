<?php

namespace App\Tests\Functional\Controller;

use App\Controller\FaxController;
use App\Service\FaxService;
use App\Tests\Functional\AbstractControllerBaseWebTestCase;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\Request;

class FaxControllerTest extends AbstractControllerBaseWebTestCase
{
    private const BASE_ADDRESS = '/fax';

    public function testSendForm(): void
    {
        $this->loadTestFixtures();

        $client = $this->getClient();

        $client->request(
            'GET',
            self::BASE_ADDRESS . '/send'
        );

        self::assertSame(200, $client->getResponse()->getStatusCode());
    }

    public function testSendFormWithData(): void
    {
        self::bootKernel();

        /** @var FaxService|\PHPUnit_Framework_MockObject_MockObject $faxServiceMock */
        $faxServiceMock = $this->createMock(FaxService::class);

        $sut = new FaxController();
        $sut->setContainer(self::$container);

        $request = $this->prepareRequestForSend();
        $response = $sut->send(
            $request,
            self::$container->get(EntityManagerInterface::class),
            $faxServiceMock
        );

        self::assertSame(200, $response->getStatusCode());
    }

    public function testSendFormWithDataFailingFileUpload(): void
    {
        self::bootKernel();

        /** @var FaxService|\PHPUnit_Framework_MockObject_MockObject $faxServiceMock */
        $faxServiceMock = $this->createMock(FaxService::class);

        $sut = new FaxController();
        $sut->setContainer(self::$container);

        $request = $this->prepareRequestForSend(1);
        $response = $sut->send(
            $request,
            self::$container->get(EntityManagerInterface::class),
            $faxServiceMock
        );

        self::assertSame(200, $response->getStatusCode());
    }

    public function testGetMediaFile(): void
    {
        $this->loadTestFixtures([
            __DIR__ . '/../Fixtures/Fax.yml',
        ]);

        $client = $this->getClient();

        $client->request(
            'GET',
            self::BASE_ADDRESS . '/file/1'
        );

        self::assertSame(200, $client->getResponse()->getStatusCode());
    }

    /**
     * @param int $fileErrorCode
     *
     * @return Request
     */
    private function prepareRequestForSend(int $fileErrorCode = 0): Request
    {
        $request = new Request([], [
            'fax_send' => [
                'toPhoneNumber' => '+49123456789',
            ],
        ]);
        copy(__DIR__ . '/Api/Data/someFile.pdf', __DIR__ . '/Api/Data/dummy.pdf');
        $file = new UploadedFile(
            __DIR__ . '/Api/Data/dummy.pdf',
            'someFile.pdf',
            'application/pdf',
            $fileErrorCode,
            true
        );
        $request->files->add([
            'fax_send' => [
                'file' => $file,
            ],
        ]);
        $request->setMethod('POST');

        return $request;
    }
}
