<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Table(
 *     name="user_roles"
 * )
 * @ORM\Entity
 */
class UserRole extends AbstractChangeable
{
    public const ROLE_USER = 'ROLE_USER';
    public const ROLE_ADMIN = 'ROLE_ADMIN';

    /**
     * @var string
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="NONE")
     * @ORM\Column(type="string", length=191, nullable=false)
     */
    private $code;

    /**
     * @var string
     * @ORM\Column(type="string", length=512, nullable=false)
     */
    private $name;

    /**
     * @var User[]|Collection
     * @ORM\ManyToMany(targetEntity="User", fetch="EXTRA_LAZY", mappedBy="roles", cascade={"persist"})
     */
    private $users;

    public function __construct()
    {
        parent::__construct();

        $this->users = new ArrayCollection();
    }

    /**
     * @return string
     */
    public function getId(): string
    {
        return $this->code;
    }

    /**
     * @return string
     */
    public function getCode(): string
    {
        return $this->code;
    }

    /**
     * @param string $code
     *
     * @return $this
     */
    public function setCode(string $code): self
    {
        $this->code = $code;

        return $this;
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @param string $name
     *
     * @return $this
     */
    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    /**
     * @return User[]|Collection
     */
    public function getUsers(): Collection
    {
        return $this->users;
    }

    /**
     * @param User $user
     *
     * @return $this
     */
    public function addUser(User $user): self
    {
        if ($this->users->contains($user)) {
            return $this;
        }
        $this->users->add($user);
        $user->addUserRole($this);

        return $this;
    }

    /**
     * @param User $user
     *
     * @return $this
     */
    public function removeUser(User $user): self
    {
        if (!$this->users->contains($user)) {
            return $this;
        }
        $this->users->removeElement($user);
        $user->removeUserRole($this);

        return $this;
    }

    /**
     * @param User[] $users
     *
     * @return $this
     */
    public function setUsers(array $users): self
    {
        $this->users = new ArrayCollection($users);

        return $this;
    }
}
