<?php

namespace App\Service\ResponseHandling;

use JMS\Serializer\SerializationContext;
use JMS\Serializer\SerializerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;

class ResponseService
{
    public const GROUP_DEFAULT = 'Default';
    public const GROUP_DEBUG = 'debug';

    public const FORMAT_JSON = 'json';
    public const FORMAT_XML = 'xml';

    /** @var SerializerInterface */
    private $serializer;

    /** @var bool */
    private $isDebug = false;

    /** @var int */
    private $version = 1;

    /** @var string[] */
    private $groups = [
        self::GROUP_DEFAULT,
    ];

    /**
     * @param SerializerInterface $serializer
     */
    public function __construct(SerializerInterface $serializer)
    {
        $this->serializer = $serializer;
    }

    /**
     * @param string     $errorMessage
     * @param int        $errorCode
     * @param null|mixed $data
     * @param int        $httpStatusCode
     *
     * @return Response
     */
    public function createJsonErrorResponse(
        string $errorMessage,
        int $errorCode,
        $data = null,
        int $httpStatusCode = 400
    ): Response {
        $responseStructure = new ResponseStructure();
        $responseStructure->setErrorMessage($errorMessage)
            ->setErrorCode($errorCode)
            ->setStatus(ResponseStructure::STATUS_ERROR)
            ->setData($data);

        return $this->createOutputResponse(self::FORMAT_JSON, $responseStructure, $httpStatusCode);
    }

    /**
     * @param null|mixed $data
     * @param int        $httpStatusCode
     *
     * @return Response
     */
    public function createJsonSuccessResponse($data = null, int $httpStatusCode = 200): Response
    {
        $responseStructure = new ResponseStructure();
        $responseStructure->setData($data)
            ->setStatus(ResponseStructure::STATUS_OK);

        return $this->createOutputResponse(self::FORMAT_JSON, $responseStructure, $httpStatusCode);
    }

    /**
     * @param string            $format
     * @param ResponseStructure $responseStructure
     * @param int               $httpStatusCode
     *
     * @return Response
     */
    public function createOutputResponse(
        string $format,
        ResponseStructure $responseStructure,
        int $httpStatusCode = 200
    ): Response {
        $jsonString = $this->serializer->serialize(
            $responseStructure,
            $format,
            $this->buildSerializerContext()
        );

        return JsonResponse::fromJsonString(
            $jsonString,
            $httpStatusCode
        );
    }

    /**
     * @return bool
     */
    public function isDebug(): bool
    {
        return $this->isDebug;
    }

    /**
     * @param bool $isDebug
     *
     * @return $this
     */
    public function setIsDebug(bool $isDebug): self
    {
        $this->isDebug = $isDebug;

        return $this;
    }

    /**
     * @return int
     */
    public function getVersion(): int
    {
        return $this->version;
    }

    /**
     * @param int $version
     *
     * @return $this
     */
    public function setVersion(int $version): self
    {
        $this->version = $version;

        return $this;
    }

    /**
     * @param string[] $groups
     *
     * @return $this
     */
    public function setGroups(array $groups): self
    {
        $this->groups = $groups;

        return $this;
    }

    /**
     * @param string $group
     *
     * @return $this
     */
    public function addGroup(string $group): self
    {
        if (!in_array($group, $this->groups, true)) {
            $this->groups[] = $group;
        }

        return $this;
    }

    /**
     * @param string $group
     *
     * @return $this
     */
    public function removeGroup(string $group): self
    {
        $foundKey = array_search($group, $this->groups, true);
        if (false !== $foundKey) {
            unset($this->groups[$foundKey]);
        }

        return $this;
    }

    /**
     * @return SerializationContext
     */
    private function buildSerializerContext(): SerializationContext
    {
        $serializerContext = SerializationContext::create();
        $serializerContext->setSerializeNull(true)
            ->setVersion($this->version);

        if (true === $this->isDebug) {
            $this->groups[] = self::GROUP_DEBUG;
        }

        if (!empty($this->groups)) {
            $serializerContext->setGroups($this->groups);
        }

        return $serializerContext;
    }
}
