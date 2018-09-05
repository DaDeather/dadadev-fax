<?php

namespace App\Tests\Unit\Entity;

use App\Entity\Fax;
use PHPUnit\Framework\TestCase;

class FaxTest extends TestCase
{
    private const SOME_DURATION = 5;
    private const SOME_FAX_STATUS = 'someFaxStatus';
    private const SOME_REMOTE_STATION_ID = 'someRemoteStationId';
    private const SOME_FAX_ID = 'someFaxId';
    private const SOME_FILE_MIME = 'someFileMime';
    private const SOME_FILE_PATH = 'someFilePath';
    private const SOME_PRICE_UNIT = 'somePriceUnit';
    private const SOME_MEDIA_ID = 'someMediaId';
    private const SOME_MEDIA_URL = 'someMediaUrl';
    private const SOME_FAX_DIRECTION = 'someFaxDirection';
    private const SOME_PRICE = 0.35;
    private const SOME_FILE_SIZE = 123456;

    /**
     * @var Fax
     */
    private $sut;

    public function testSetterAndGetter()
    {
        $this->sut = new Fax();

        $this->sut->setFaxId(self::SOME_FAX_ID)
            ->setLocalFileSizeInBytes(self::SOME_FILE_SIZE)
            ->setLocalFileMime(self::SOME_FILE_MIME)
            ->setLocalFilePath(self::SOME_FILE_PATH)
            ->setPrice(self::SOME_PRICE)
            ->setPriceUnit(self::SOME_PRICE_UNIT)
            ->setMediaId(self::SOME_MEDIA_ID)
            ->setMediaUrl(self::SOME_MEDIA_URL)
            ->setFaxDirection(self::SOME_FAX_DIRECTION)
            ->setDuration(self::SOME_DURATION)
            ->setFaxStatus(self::SOME_FAX_STATUS)
            ->setRemoteStationId(self::SOME_REMOTE_STATION_ID);

        self::assertSame(self::SOME_FAX_ID, $this->sut->getFaxId());
        self::assertSame(self::SOME_FILE_SIZE, $this->sut->getLocalFileSizeInBytes());
        self::assertSame(self::SOME_FILE_MIME, $this->sut->getLocalFileMime());
        self::assertSame(self::SOME_FILE_PATH, $this->sut->getLocalFilePath());
        self::assertSame(self::SOME_PRICE, $this->sut->getPrice());
        self::assertSame(self::SOME_PRICE_UNIT, $this->sut->getPriceUnit());
        self::assertSame(self::SOME_MEDIA_ID, $this->sut->getMediaId());
        self::assertSame(self::SOME_MEDIA_URL, $this->sut->getMediaUrl());
        self::assertSame(self::SOME_FAX_DIRECTION, $this->sut->getFaxDirection());
        self::assertSame(self::SOME_DURATION, $this->sut->getDuration());
        self::assertSame(self::SOME_FAX_STATUS, $this->sut->getFaxStatus());
        self::assertSame(self::SOME_REMOTE_STATION_ID, $this->sut->getRemoteStationId());
    }
}
