<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use JMS\Serializer\Annotation as Serializer;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Table(
 *     name="users"
 * )
 * @ORM\Entity
 */
class User extends AbstractChangeable implements UserInterface, \Serializable
{
    /**
     * @var int
     *
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @var string|null
     *
     * @Assert\NotBlank()
     * @Assert\Email(message = "The email '{{ value }}' is not a valid email.")
     *
     * @ORM\Column(type="string", length=191, unique=true, nullable=false)
     */
    private $email;

    /**
     * @var string|null
     *
     * @Serializer\Exclude()
     *
     * @Assert\NotBlank()
     * @Assert\Length(
     *     min="6",
     *     max="32"
     * )
     *
     * @ORM\Column(type="string", length=512, nullable=false)
     */
    private $password;

    /**
     * @var string|null
     *
     * @Assert\NotBlank()
     * @Assert\Length(
     *     min="2",
     *     max="255"
     * )
     *
     * @ORM\Column(type="string", length=255, nullable=false)
     */
    private $forename;

    /**
     * @var string|null
     *
     * @Assert\NotBlank()
     * @Assert\Length(
     *     min="2",
     *     max="255"
     * )
     *
     * @ORM\Column(type="string", length=255, nullable=false)
     */
    private $surname;

    /**
     * @var bool
     *
     * @ORM\Column(type="boolean", nullable=false, options={"default": true})
     */
    private $active = true;

    /**
     * @var \DateTime
     *
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $lastLogin;

    /**
     * @var Collection|UserRole[]
     *
     * @ORM\ManyToMany(targetEntity="UserRole", inversedBy="users", cascade={"persist"})
     * @ORM\JoinTable(name="user_roles_to_users",
     *   joinColumns={
     *     @ORM\JoinColumn(name="user_id", referencedColumnName="id")
     *   },
     *   inverseJoinColumns={
     *     @ORM\JoinColumn(name="user_role_code", referencedColumnName="code")
     *   }
     * )
     */
    private $roles;

    public function __construct()
    {
        parent::__construct();

        $this->roles = new ArrayCollection();
    }

    // Necessary functions

    /**
     * @return string|null
     */
    public function getUsername(): ?string
    {
        return $this->email;
    }

    /**
     * @return null|string
     */
    public function getSalt(): ?string
    {
        return null;
    }

    /**
     * @return string|null
     */
    public function getPassword(): ?string
    {
        return $this->password;
    }

    /**
     * @return string[]
     */
    public function getRoles()
    {
        // If no role exists set the role user as default
        if (0 === $this->roles->count()) {
            return [
                UserRole::ROLE_USER,
            ];
        }

        // Get the roles
        $roles = $this->roles->toArray();
        $returnRoles = [];

        /* @var $role UserRole */
        foreach ($roles as $role) {
            $returnRoles[] = $role->getCode();
        }

        return $returnRoles;
    }

    /**
     * @return Collection|UserRole[]
     */
    public function getRolesAsEntities(): Collection
    {
        return $this->roles;
    }

    /**
     * @param UserRole[] $roles
     *
     * @return $this
     */
    public function setRoles(array $roles): self
    {
        $this->roles = new ArrayCollection($roles);

        return $this;
    }

    public function eraseCredentials(): void
    {
    }

    /** @see \Serializable::serialize() */
    public function serialize(): ?string
    {
        return serialize([
            $this->id,
            $this->email,
            $this->password,
            $this->getRolesAsEntities(),
            $this->created->format('Y-m-d H:i:s'),
            $this->updated ? $this->updated->format('Y-m-d H:i:s') : null,
            $this->deleted ? $this->deleted->format('Y-m-d H:i:s') : null,
        ]);
    }

    /**
     * @see \Serializable::unserialize()
     *
     * @param string $serialized
     */
    public function unserialize($serialized): void
    {
        [
            $this->id,
            $this->email,
            $this->password,
            $this->roles,
            $created,
            $updated,
            $deleted,
        ] = unserialize($serialized);

        $this->created = new \DateTime($created);
        $this->updated = ($updated ? new \DateTime($updated) : null);
        $this->deleted = ($deleted ? new \DateTime($deleted) : null);
    }

    // Getter and setter

    /**
     * @return int|null
     */
    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * @return string|null
     */
    public function getEmail(): ?string
    {
        return $this->email;
    }

    /**
     * @param string|null $password
     *
     * @return $this
     */
    public function setPassword(?string $password = null): self
    {
        $this->password = $password;

        return $this;
    }

    /**
     * @param string|null $email
     *
     * @return $this
     */
    public function setEmail(?string $email = null): self
    {
        $this->email = $email;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getForename(): ?string
    {
        return $this->forename;
    }

    /**
     * @param string|null $forename
     *
     * @return $this
     */
    public function setForename(?string $forename = null): self
    {
        $this->forename = $forename;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getSurname(): ?string
    {
        return $this->surname;
    }

    /**
     * @param string|null $surname
     *
     * @return $this
     */
    public function setSurname(?string $surname = null): self
    {
        $this->surname = $surname;

        return $this;
    }

    /**
     * @return bool
     */
    public function isActive(): bool
    {
        return $this->active;
    }

    /**
     * @param bool $active
     *
     * @return $this
     */
    public function setActive(bool $active): self
    {
        $this->active = $active;

        return $this;
    }

    /**
     * @return \DateTime
     */
    public function getLastLogin(): \DateTime
    {
        return $this->lastLogin;
    }

    /**
     * @param \DateTime $lastLogin
     *
     * @return $this
     */
    public function setLastLogin(\DateTime $lastLogin): self
    {
        $this->lastLogin = $lastLogin;

        return $this;
    }

    /**
     * @param UserRole $userRole
     *
     * @return $this
     */
    public function addUserRole(UserRole $userRole): self
    {
        if ($this->roles->contains($userRole)) {
            return $this;
        }
        $this->roles->add($userRole);
        $userRole->addUser($this);

        return $this;
    }

    /**
     * @param UserRole $userRole
     *
     * @return $this
     */
    public function removeUserRole(UserRole $userRole): self
    {
        if (!$this->roles->contains($userRole)) {
            return $this;
        }
        $this->roles->removeElement($userRole);
        $userRole->removeUser($this);

        return $this;
    }
}
