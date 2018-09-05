<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Table(
 *     name="faxes"
 * )
 * @ORM\Entity(repositoryClass="App\Repository\FaxRepository")
 */
class Fax extends AbstractChangeable
{
    /** The fax is queued, waiting for processing. */
    public const STATUS_QUEUED = 'queued';
    /** The fax is being downloaded, uploaded, or transcoded into a different format. */
    public const STATUS_PROCESSING = 'processing';
    /** The fax is in the process of being sent. */
    public const STATUS_SENDING = 'sending';
    /** The fax has been successfuly delivered. */
    public const STATUS_DELIVERED = 'delivered';
    /** The fax is in the process of being received */
    public const STATUS_RECEIVING = 'receiving';
    /** The fax has been successfully received. */
    public const STATUS_RECEIVED = 'received';
    /** The outbound fax failed because the other end did not pick up. */
    public const STATUS_NO_ANSWER = 'no-answer';
    /** The outbound fax failed because the other side sent back a busy signal. */
    public const STATUS_BUSY = 'busy';
    /** The fax failed to send or receive. */
    public const STATUS_FAILED = 'failed';
    /** The fax was canceled, either by using the REST API, or rejected by TwiML. */
    public const STATUS_CANCELED = 'canceled';

    /** Contains all unfinished states. */
    public const UNFINISHED_STATE_COLLECTION = [
        self::STATUS_QUEUED,
        self::STATUS_PROCESSING,
        self::STATUS_SENDING,
        self::STATUS_RECEIVING,
    ];

    /** Contains all finished states-  */
    public const FINISHED_STATE_COLLECTION = [
        self::STATUS_DELIVERED,
        self::STATUS_RECEIVED,
        self::STATUS_NO_ANSWER,
        self::STATUS_BUSY,
        self::STATUS_FAILED,
        self::STATUS_CANCELED,
    ];

    /** Contains all finished states-  */
    public const FINISHED_RECEIVE_FAILED_STATE_COLLECTION = [
        self::STATUS_FAILED,
        self::STATUS_CANCELED,
    ];

    public const DIRECTION_INBOUND = 'inbound';
    public const DIRECTION_OUTBOUND = 'outbound';

    /**
     * @var int|null
     *
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @var string|null
     *
     * @ORM\Column(type="string", length=50, unique=true)
     */
    private $faxId;

    /**
     * @var string|null
     *
     * @ORM\Column(type="string", length=25)
     */
    private $faxDirection;

    /**
     * @var int|null
     *
     * @ORM\Column(type="integer", nullable=true)
     */
    private $duration;

    /**
     * @var string|null
     *
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $fromPhoneNumber;

    /**
     * @var string|null
     *
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $toPhoneNumber;

    /**
     * @var int|null
     *
     * @ORM\Column(type="integer", nullable=true)
     */
    private $pagesCount;

    /**
     * @var float|null
     *
     * @ORM\Column(type="decimal", precision=10, scale=2, nullable=true)
     */
    private $price;

    /**
     * @var string|null
     *
     * @ORM\Column(type="string", length=3, nullable=true)
     */
    private $priceUnit;

    /**
     * @var string|null
     *
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $remoteStationId;

    /**
     * @var string|null
     *
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $faxStatus;

    /**
     * @var string|null
     *
     * @ORM\Column(type="string", length=50, nullable=true)
     */
    private $mediaId;

    /**
     * @var string|null
     *
     * @ORM\Column(type="text", length=65535, nullable=true)
     */
    private $mediaUrl;

    /**
     * @var string|null
     *
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $localFilePath;

    /**
     * @var string|null
     *
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $localFileMime;

    /**
     * @var int|null
     *
     * @ORM\Column(type="integer", nullable=true)
     */
    private $localFileSizeInBytes;

    /**
     * @return int|null
     */
    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * @return null|string
     */
    public function getFaxId(): ?string
    {
        return $this->faxId;
    }

    /**
     * @param null|string $faxId
     *
     * @return $this
     */
    public function setFaxId(?string $faxId): self
    {
        $this->faxId = $faxId;

        return $this;
    }

    /**
     * @return null|string
     */
    public function getFaxDirection(): ?string
    {
        return $this->faxDirection;
    }

    /**
     * @param null|string $faxDirection
     *
     * @return $this
     */
    public function setFaxDirection(?string $faxDirection): self
    {
        $this->faxDirection = $faxDirection;

        return $this;
    }

    /**
     * @return int|null
     */
    public function getDuration(): ?int
    {
        return $this->duration;
    }

    /**
     * @param int|null $duration
     *
     * @return $this
     */
    public function setDuration(?int $duration): self
    {
        $this->duration = $duration;

        return $this;
    }

    /**
     * @return null|string
     */
    public function getFromPhoneNumber(): ?string
    {
        return $this->fromPhoneNumber;
    }

    /**
     * @param null|string $fromPhoneNumber
     *
     * @return $this
     */
    public function setFromPhoneNumber(?string $fromPhoneNumber): self
    {
        $this->fromPhoneNumber = $fromPhoneNumber;

        return $this;
    }

    /**
     * @return null|string
     */
    public function getToPhoneNumber(): ?string
    {
        return $this->toPhoneNumber;
    }

    /**
     * @param null|string $toPhoneNumber
     *
     * @return $this
     */
    public function setToPhoneNumber(?string $toPhoneNumber): self
    {
        $this->toPhoneNumber = $toPhoneNumber;

        return $this;
    }

    /**
     * @return int|null
     */
    public function getPagesCount(): ?int
    {
        return $this->pagesCount;
    }

    /**
     * @param int|null $pagesCount
     *
     * @return $this
     */
    public function setPagesCount(?int $pagesCount): self
    {
        $this->pagesCount = $pagesCount;

        return $this;
    }

    /**
     * @return float|null
     */
    public function getPrice(): ?float
    {
        return $this->price;
    }

    /**
     * @param float|null $price
     *
     * @return $this
     */
    public function setPrice(?float $price): self
    {
        $this->price = $price;

        return $this;
    }

    /**
     * @return null|string
     */
    public function getPriceUnit(): ?string
    {
        return $this->priceUnit;
    }

    /**
     * @param null|string $priceUnit
     *
     * @return $this
     */
    public function setPriceUnit(?string $priceUnit): self
    {
        $this->priceUnit = $priceUnit;

        return $this;
    }

    /**
     * @return null|string
     */
    public function getRemoteStationId(): ?string
    {
        return $this->remoteStationId;
    }

    /**
     * @param null|string $remoteStationId
     *
     * @return $this
     */
    public function setRemoteStationId(?string $remoteStationId): self
    {
        $this->remoteStationId = $remoteStationId;

        return $this;
    }

    /**
     * @return null|string
     */
    public function getFaxStatus(): ?string
    {
        return $this->faxStatus;
    }

    /**
     * @param null|string $faxStatus
     *
     * @return $this
     */
    public function setFaxStatus(?string $faxStatus): self
    {
        $this->faxStatus = $faxStatus;

        return $this;
    }

    /**
     * @return null|string
     */
    public function getMediaId(): ?string
    {
        return $this->mediaId;
    }

    /**
     * @param null|string $mediaId
     *
     * @return $this
     */
    public function setMediaId(?string $mediaId): self
    {
        $this->mediaId = $mediaId;

        return $this;
    }

    /**
     * @return null|string
     */
    public function getMediaUrl(): ?string
    {
        return $this->mediaUrl;
    }

    /**
     * @param null|string $mediaUrl
     *
     * @return $this
     */
    public function setMediaUrl(?string $mediaUrl): self
    {
        $this->mediaUrl = $mediaUrl;

        return $this;
    }

    /**
     * @return null|string
     */
    public function getLocalFilePath(): ?string
    {
        return $this->localFilePath;
    }

    /**
     * @param null|string $localFilePath
     *
     * @return $this
     */
    public function setLocalFilePath(?string $localFilePath): self
    {
        $this->localFilePath = $localFilePath;

        return $this;
    }

    /**
     * @return null|string
     */
    public function getLocalFileMime(): ?string
    {
        return $this->localFileMime;
    }

    /**
     * @param null|string $localFileMime
     *
     * @return $this
     */
    public function setLocalFileMime(?string $localFileMime): self
    {
        $this->localFileMime = $localFileMime;

        return $this;
    }

    /**
     * @return int|null
     */
    public function getLocalFileSizeInBytes(): ?int
    {
        return $this->localFileSizeInBytes;
    }

    /**
     * @param int|null $localFileSizeInBytes
     *
     * @return $this
     */
    public function setLocalFileSizeInBytes(?int $localFileSizeInBytes): self
    {
        $this->localFileSizeInBytes = $localFileSizeInBytes;

        return $this;
    }
}
