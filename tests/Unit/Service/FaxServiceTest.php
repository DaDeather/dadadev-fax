<?php

namespace App\Tests\Unit\Service;

use App\Entity\Fax;
use App\Repository\FaxRepository;
use App\Service\FaxService;
use Doctrine\ORM\EntityManager;
use PHPUnit\Framework\TestCase;
use Twilio\Exceptions\TwilioException;
use Twilio\Rest\Client;
use Twilio\Rest\Fax\V1\FaxContext;
use Twilio\Rest\Fax\V1\FaxInstance;
use Twilio\Rest\Fax\V1\FaxList;

class FaxServiceTest extends TestCase
{
    /**
     * @var FaxService
     */
    private $sut;

    /** @var \PHPUnit_Framework_MockObject_MockObject|Client|\stdClass */
    private $twilioClientMock;
    /** @var \PHPUnit_Framework_MockObject_MockObject|EntityManager */
    private $entityManagerMock;
    /** @var \PHPUnit_Framework_MockObject_MockObject|FaxRepository */
    private $faxRepositoryMock;

    protected function setUp(): void
    {
        $this->twilioClientMock = $this->createMock(Client::class);
        $this->entityManagerMock = $this->createMock(EntityManager::class);
        $this->faxRepositoryMock = $this->createMock(FaxRepository::class);

        $this->sut = new FaxService(
            'someFromPhoneNumber',
            $this->twilioClientMock,
            $this->entityManagerMock,
            $this->faxRepositoryMock
        );
    }

    public function testSendFax(): void
    {
        /** @var \stdClass|\PHPUnit_Framework_MockObject_MockObject $faxInstanceMock */
        $faxInstanceMock = $this->createMock(FaxInstance::class);
        $faxInstanceMock->sid = 'someFaxSid';
        $faxInstanceMock->status = 'someFaxStatus';

        /** @var FaxList|\PHPUnit_Framework_MockObject_MockObject $faxesMock */
        $faxesMock = $this->createMock(FaxList::class);
        $this->twilioClientMock->fax = new \stdClass();
        $this->twilioClientMock->fax->v1 = new \stdClass();
        $this->twilioClientMock->fax->v1->faxes = $faxesMock;
        $faxesMock->expects(self::once())
            ->method('create')
            ->with('someToPhoneNumber', 'someDocumentUrl', ['from' => 'someFromPhoneNumber'])
            ->willReturn($faxInstanceMock);

        $fax = $this->sut->sendFax('someToPhoneNumber', 'someDocumentUrl');

        self::assertSame('someFromPhoneNumber', $fax->getFromPhoneNumber());
        self::assertSame('someToPhoneNumber', $fax->getToPhoneNumber());
        self::assertSame(Fax::DIRECTION_OUTBOUND, $fax->getFaxDirection());
        self::assertSame($faxInstanceMock->sid, $fax->getFaxId());
        self::assertSame($faxInstanceMock->status, $fax->getFaxStatus());
    }

    public function testUpdateAndGetFaxSuccessfully(): void
    {
        $faxInstanceMock = $this->createFaxInstanceMock();

        /** @var FaxContext|\PHPUnit_Framework_MockObject_MockObject $faxContextMock */
        $faxContextMock = $this->createMock(FaxContext::class);
        $faxContextMock->expects(self::once())->method('fetch')->willReturn($faxInstanceMock);

        $this->twilioClientMock->fax = new \stdClass();
        $this->twilioClientMock->fax->v1 = $faxesMock = $this->getMockBuilder(\stdClass::class)
            ->setMethods([
                'faxes',
            ])
            ->getMock();
        $faxesMock->expects(self::once())->method('faxes')->willReturn($faxContextMock);

        /** @var Fax $fax */
        $fax = $this->sut->updateAndGetFax('someFaxSid');

        self::assertSame($faxInstanceMock->sid, $fax->getFaxId());
        self::assertSame($faxInstanceMock->status, $fax->getFaxStatus());
        self::assertSame($faxInstanceMock->direction, $fax->getFaxDirection());
        self::assertSame($faxInstanceMock->from, $fax->getFromPhoneNumber());
        self::assertSame($faxInstanceMock->to, $fax->getToPhoneNumber());
        self::assertSame($faxInstanceMock->duration, $fax->getDuration());
        self::assertSame($faxInstanceMock->mediaUrl, $fax->getMediaUrl());
        self::assertSame($faxInstanceMock->mediaSid, $fax->getMediaId());
        self::assertSame($faxInstanceMock->numPages, $fax->getPagesCount());
        self::assertSame($faxInstanceMock->price, $fax->getPrice());
        self::assertSame($faxInstanceMock->priceUnit, $fax->getPriceUnit());
    }

    public function testUpdateAndGetFaxSuccessfullyWithExistingFaxEntity(): void
    {
        $faxInstanceMock = $this->createFaxInstanceMock();

        $faxEntity = new Fax();
        $this->faxRepositoryMock->expects(self::once())->method('findFaxByFaxId')->willReturn($faxEntity);

        /** @var FaxContext|\PHPUnit_Framework_MockObject_MockObject $faxContextMock */
        $faxContextMock = $this->createMock(FaxContext::class);
        $faxContextMock->expects(self::once())->method('fetch')->willReturn($faxInstanceMock);

        $this->twilioClientMock->fax = new \stdClass();
        $this->twilioClientMock->fax->v1 = $faxesMock = $this->getMockBuilder(\stdClass::class)
            ->setMethods([
                'faxes',
            ])
            ->getMock();
        $faxesMock->expects(self::once())->method('faxes')->willReturn($faxContextMock);

        /** @var Fax $fax */
        $fax = $this->sut->updateAndGetFax('someFaxSid');

        self::assertSame($faxInstanceMock->sid, $fax->getFaxId());
        self::assertSame($faxInstanceMock->status, $fax->getFaxStatus());
        self::assertSame($faxInstanceMock->direction, $fax->getFaxDirection());
        self::assertSame($faxInstanceMock->from, $fax->getFromPhoneNumber());
        self::assertSame($faxInstanceMock->to, $fax->getToPhoneNumber());
        self::assertSame($faxInstanceMock->duration, $fax->getDuration());
        self::assertSame($faxInstanceMock->mediaUrl, $fax->getMediaUrl());
        self::assertSame($faxInstanceMock->mediaSid, $fax->getMediaId());
        self::assertSame($faxInstanceMock->numPages, $fax->getPagesCount());
        self::assertSame($faxInstanceMock->price, $fax->getPrice());
        self::assertSame($faxInstanceMock->priceUnit, $fax->getPriceUnit());
    }

    public function testUpdateAndGetFaxFail(): void
    {
        $this->twilioClientMock->fax = new \stdClass();
        $this->twilioClientMock->fax->v1 = $faxesMock = $this->getMockBuilder(\stdClass::class)
            ->setMethods([
                'faxes',
            ])
            ->getMock();
        $faxesMock->expects(self::once())->method('faxes')->willThrowException(new TwilioException());

        $fax = $this->sut->updateAndGetFax('someFaxSid');

        self::assertNull($fax);
    }

    /**
     * @return \PHPUnit_Framework_MockObject_MockObject|\stdClass
     */
    private function createFaxInstanceMock(): \PHPUnit_Framework_MockObject_MockObject
    {
        /** @var \stdClass|\PHPUnit_Framework_MockObject_MockObject $faxInstanceMock */
        $faxInstanceMock = $this->createMock(FaxInstance::class);
        $faxInstanceMock->sid = 'someFaxSid';
        $faxInstanceMock->status = 'someFaxStatus';
        $faxInstanceMock->direction = 'someFaxStatus';
        $faxInstanceMock->from = 'someFromPhoneNumber';
        $faxInstanceMock->to = 'someToPhoneNumber';
        $faxInstanceMock->duration = 123;
        $faxInstanceMock->mediaUrl = null;
        $faxInstanceMock->mediaSid = '#123456789';
        $faxInstanceMock->numPages = 2;
        $faxInstanceMock->price = 0.032;
        $faxInstanceMock->priceUnit = 'EUR';

        return $faxInstanceMock;
    }
}
