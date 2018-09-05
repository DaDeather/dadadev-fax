<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\MappedSuperclass
 * @ORM\HasLifecycleCallbacks()
 */
abstract class AbstractChangeable
{
    /**
     * @var \DateTime
     * @ORM\Column(type="datetime", nullable=false)
     */
    protected $created;

    /**
     * @var null|\DateTime
     * @ORM\Column(type="datetime", options={"default":null}, nullable=true)
     */
    protected $updated;

    /**
     * @var null|\DateTime
     * @ORM\Column(type="datetime", options={"default":null}, nullable=true)
     */
    protected $deleted;

    public function __construct()
    {
        $this->created = new \DateTime();
    }

    /**
     * @return \DateTime
     */
    public function getCreated(): \DateTime
    {
        return $this->created;
    }

    /**
     * @param \DateTime $created
     *
     * @return $this
     */
    public function setCreated(\DateTime $created): self
    {
        $this->created = $created;

        return $this;
    }

    /**
     * @return null|\DateTime
     */
    public function getUpdated(): ?\DateTime
    {
        return $this->updated;
    }

    /**
     * @param null|\DateTime $updated
     *
     * @return $this
     */
    public function setUpdated(?\DateTime $updated = null): self
    {
        $this->updated = $updated;

        return $this;
    }

    /**
     * @return null|\DateTime
     */
    public function getDeleted(): ?\DateTime
    {
        return $this->deleted;
    }

    /**
     * @param null|\DateTime $deleted
     *
     * @return $this
     */
    public function setDeleted(?\DateTime $deleted = null): self
    {
        $this->deleted = $deleted;

        return $this;
    }

    /**
     * @ORM\PreUpdate()
     */
    public function preUpdateSetUpdatedValue()
    {
        $this->updated = new \DateTime();
    }
}
