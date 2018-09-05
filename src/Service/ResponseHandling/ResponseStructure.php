<?php

namespace App\Service\ResponseHandling;

use JMS\Serializer\Annotation as Serializer;

class ResponseStructure
{
    public const STATUS_OK = 'ok';
    public const STATUS_ERROR = 'error';
    public const STATUS_UNDEFINED = 'undef';

    /**
     * @var string
     */
    private $status = self::STATUS_UNDEFINED;

    /**
     * @var null|mixed
     */
    private $data;

    /**
     * @var null|string
     * @Serializer\SkipWhenEmpty()
     */
    private $errorMessage;

    /**
     * @var null|int
     * @Serializer\SkipWhenEmpty()
     */
    private $errorCode;

    /**
     * @var null|int
     */
    private $utcTimestamp;

    /**
     * @var float
     * @Serializer\Groups({"debug"})
     */
    private $executionDurationInMs = 0;

    /**
     * @var float
     * @Serializer\Groups({"debug"})
     */
    private $memoryUsagePeakInMb = 0;

    public function __construct($dataToOutput = null)
    {
        $this->data = $dataToOutput;
    }

    /**
     * @Serializer\PreSerialize()
     * @SuppressWarnings(PHPMD)
     */
    private function preSerialize(): void
    {
        $this->utcTimestamp = time();
        $this->executionDurationInMs = microtime(true)
            - ($_SERVER['REQUEST_TIME_FLOAT'] ?? $_SERVER['REQUEST_TIME']);
        $this->memoryUsagePeakInMb = (memory_get_peak_usage(true) / 1024.0 / 1024.0);
    }

    /**
     * @return string
     */
    public function getStatus(): string
    {
        return $this->status;
    }

    /**
     * @param string $status
     *
     * @return $this
     */
    public function setStatus(string $status): self
    {
        $this->status = $status;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getData()
    {
        return $this->data;
    }

    /**
     * @param mixed $data
     *
     * @return $this
     */
    public function setData($data)
    {
        $this->data = $data;

        return $this;
    }

    /**
     * @return null|string
     */
    public function getErrorMessage(): ?string
    {
        return $this->errorMessage;
    }

    /**
     * @param null|string $errorMessage
     *
     * @return $this
     */
    public function setErrorMessage(?string $errorMessage): self
    {
        $this->errorMessage = $errorMessage;

        return $this;
    }

    /**
     * @return int|null
     */
    public function getErrorCode(): ?int
    {
        return $this->errorCode;
    }

    /**
     * @param int|null $errorCode
     *
     * @return $this
     */
    public function setErrorCode(?int $errorCode): self
    {
        $this->errorCode = $errorCode;

        return $this;
    }
}
