<?php

namespace App\Entity;

use App\Repository\UsersRepository;
use App\Trait\TimeStampTrait;
use DateTimeInterface;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use libphonenumber\PhoneNumber;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Validator\Constraints as Assert;
use Misd\PhoneNumberBundle\Validator\Constraints\PhoneNumber as AssertPhoneNumber;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;

#[UniqueEntity('username')]
#[ORM\Entity(repositoryClass: UsersRepository::class)]
#[ORM\HasLifecycleCallbacks]
class Users implements UserInterface, PasswordAuthenticatedUserInterface
{
    use TimeStampTrait;

    /**
     * @var int|null
     */
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    /**
     * @var string|null
     */
    #[ORM\Column(length: 50, unique: true, nullable: false)]
    #[Assert\NotBlank]
    #[Assert\Length(min:2, max: 50)]
    private ?string $username = null;

    /**
     * @var UsersCivilities|null
     */
    #[ORM\ManyToOne(inversedBy: 'users')]
    #[ORM\JoinColumn(nullable: false)]
    private ?UsersCivilities $civility = null;

    /**
     * @var string|null
     */
    #[ORM\Column(length: 50, nullable: true)]
    #[Assert\Length(max: 50)]
    private ?string $firstname = null;

    /**
     * @var string|null
     */
    #[ORM\Column(length: 50, nullable: true)]
    #[Assert\Length(max: 50)]
    private ?string $lastname = null;

    /**
     * @var UsersPermissions|null
     */
    #[ORM\ManyToOne(inversedBy: 'users')]
    #[ORM\JoinColumn(nullable: false)]
    #[Assert\NotNull]
    private ?UsersPermissions $usersPermission = null;

    /**
     * The hashed password
     * @var string|null The hashed password
     */
    #[ORM\Column(length: 255, nullable: false)]
    #[Assert\Length(min: 2, max: 255)]
    private ?string $password = null;

    /**
     * The plain password
     * @var string|null
     */
    private ?string $plainPassword = null;

    /**
     * @var ArrayCollection|Collection
     */
    #[ORM\ManyToMany(targetEntity: Stores::class, inversedBy: 'users')]
    private Collection|ArrayCollection $stores;

    /**
     * @var DateTimeInterface|null
     */
    #[ORM\Column(type: Types::DATETIME_MUTABLE, nullable: true)]
    private ?DateTimeInterface $birthday_date = null;

    /**
     * @var DateTimeInterface|null
     */
    #[ORM\Column(type: Types::DATETIME_MUTABLE, nullable: true)]
    private ?DateTimeInterface $hiring_date = null;

    /**
     * @var PhoneNumber|null
     */
    #[ORM\Column(type: 'phone_number', nullable: true)]
    #[AssertPhoneNumber]
    private ?PhoneNumber $phone = null;

    /**
     * @var string|null
     */
    #[ORM\Column(length: 180, nullable: true)]
    #[Assert\Email]
    #[Assert\Length(max: 180)]
    private ?string $email = null;

    /**
     * @var bool|null
     */
    #[ORM\Column(nullable: false)]
    private ?bool $active = true;

    /**
     * @var ArrayCollection|Collection
     */
    #[ORM\OneToMany(mappedBy: 'user', targetEntity: Controls::class)]
    private Collection|ArrayCollection $controls;

    /**
     * @var ArrayCollection|Collection
     */
    #[ORM\OneToMany(mappedBy: 'user', targetEntity: SafesMovements::class)]
    private Collection|ArrayCollection $safesMovements;

    /**
     * @var ArrayCollection|Collection
     */
    #[ORM\OneToMany(mappedBy: 'user', targetEntity: SafesControls::class)]
    private Collection|ArrayCollection $safesControls;

    /**
     * @var ArrayCollection|Collection
     */
    #[ORM\OneToMany(mappedBy: 'user', targetEntity: Buybacks::class)]
    private Collection|ArrayCollection $buybacks;

    /**
     * @var ArrayCollection|Collection
     */
    #[ORM\OneToMany(mappedBy: 'user', targetEntity: DepositsSales::class)]
    private Collection|ArrayCollection $depositsSales;

    #[ORM\OneToMany(mappedBy: 'user', targetEntity: AdvancesPayments::class)]
    private Collection $advancesPayments;

    public function __construct()
    {
        $this->stores = new ArrayCollection();
        $this->controls = new ArrayCollection();
        $this->safesMovements = new ArrayCollection();
        $this->safesControls = new ArrayCollection();
        $this->buybacks = new ArrayCollection();
        $this->depositsSales = new ArrayCollection();
        $this->advancesPayments = new ArrayCollection();
    }

    /**
     * @return string
     */
    public function __toString(): string
    {
        return $this->getUsername();
    }

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
    public function getUsername(): ?string
    {
        return $this->username ? ucfirst($this->username) : null;
    }

    /**
     * @param string $username
     * @return $this
     */
    public function setUsername(string $username): self
    {
        $this->username = strtolower($username);

        return $this;
    }

    /**
     * A visual identifier that represents this user.
     * @see UserInterface
     * @return string
     */
    public function getUserIdentifier(): string
    {
        return (string) $this->username;
    }

    /**
     * @return UsersCivilities|null
     */
    public function getCivility(): ?UsersCivilities
    {
        return $this->civility;
    }

    /**
     * @param UsersCivilities|null $civility
     * @return $this
     */
    public function setCivility(?UsersCivilities $civility): self
    {
        $this->civility = $civility;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getFirstname(): ?string
    {
        return $this->firstname ? ucfirst($this->firstname) : null;
    }

    /**
     * @param string|null $firstname
     * @return $this
     */
    public function setFirstname(?string $firstname): self
    {
        $this->firstname = strtolower($firstname);

        return $this;
    }

    /**
     * @return string|null
     */
    public function getLastname(): ?string
    {
        return $this->lastname ? strtoupper($this->lastname) : null;
    }

    /**
     * @param string|null $lastname
     * @return $this
     */
    public function setLastname(?string $lastname): self
    {
        $this->lastname = strtolower($lastname);

        return $this;
    }

    /**
     * @return string|null
     */
    public function getFullname(): ?string
    {
        return (trim($this->getCivility() . ' ' . $this->getFirstname() . ' ' . $this->getLastName())) ?: null;
    }

    /**
     * @return UsersPermissions|null
     */
    public function getUsersPermission(): ?UsersPermissions
    {
        return $this->usersPermission;
    }

    /**
     * @param UsersPermissions|null $usersPermission
     * @return $this
     */
    public function setUsersPermission(?UsersPermissions $usersPermission): self
    {
        $this->usersPermission = $usersPermission;

        return $this;
    }

    /**
     * @see UserInterface
     */
    public function getRoles(): array
    {
        return [
            $this->usersPermission->getRole()
        ];
    }

    /**
     * @see PasswordAuthenticatedUserInterface
     * @return string
     */
    public function getPassword(): string
    {
        return $this->password;
    }

    /**
     * @param string $password
     * @return $this
     */
    public function setPassword(string $password): self
    {
        $this->password = $password;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getPlainPassword(): ?string
    {
        return $this->plainPassword;
    }

    /**
     * @param string|null $plainPassword
     * @return $this
     */
    public function setPlainPassword(?string $plainPassword): self
    {
        $this->plainPassword = $plainPassword;

        return $this;
    }

    /**
     * @see UserInterface
     * @return void
     */
    public function eraseCredentials(): void
    {
        $this->plainPassword = null;
    }

    /**
     * @return Collection<int, Stores>
     */
    public function getStores(): Collection
    {
        return $this->stores;
    }

    /**
     * @param Stores $store
     * @return $this
     */
    public function addStore(Stores $store): self
    {
        if (!$this->stores->contains($store)) {
            $this->stores->add($store);
        }

        return $this;
    }

    /**
     * @param Stores $store
     * @return $this
     */
    public function removeStore(Stores $store): self
    {
        $this->stores->removeElement($store);

        return $this;
    }

    /**
     * @return DateTimeInterface|null
     */
    public function getBirthdayDate(): ?DateTimeInterface
    {
        return $this->birthday_date;
    }

    /**
     * @param DateTimeInterface|null $birthday_date
     * @return $this
     */
    public function setBirthdayDate(?DateTimeInterface $birthday_date): self
    {
        $this->birthday_date = $birthday_date;

        return $this;
    }

    /**
     * @return DateTimeInterface|null
     */
    public function getHiringDate(): ?DateTimeInterface
    {
        return $this->hiring_date;
    }

    /**
     * @param DateTimeInterface|null $hiring_date
     * @return $this
     */
    public function setHiringDate(?DateTimeInterface $hiring_date): self
    {
        $this->hiring_date = $hiring_date;

        return $this;
    }

    /**
     * @return PhoneNumber|null
     */
    public function getPhone(): ?PhoneNumber
    {
        return $this->phone;
    }

    /**
     * @param PhoneNumber|null $phone
     * @return $this
     */
    public function setPhone(?PhoneNumber $phone): self
    {
        $this->phone = $phone;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getEmail(): ?string
    {
        return $this->email;
    }

    /**
     * @param string|null $email
     * @return $this
     */
    public function setEmail(?string $email): self
    {
        $this->email = strtolower($email);

        return $this;
    }

    /**
     * @return bool|null
     */
    public function isActive(): ?bool
    {
        return $this->active;
    }

    /**
     * @param bool $active
     * @return $this
     */
    public function setActive(bool $active): self
    {
        $this->active = $active;

        return $this;
    }

    /**
     * @return Collection<int, Controls>
     */
    public function getControls(): Collection
    {
        return $this->controls;
    }

    /**
     * @param Controls $control
     * @return $this
     */
    public function addControl(Controls $control): self
    {
        if (!$this->controls->contains($control)) {
            $this->controls->add($control);
            $control->setUser($this);
        }

        return $this;
    }

    /**
     * @param Controls $control
     * @return $this
     */
    public function removeControl(Controls $control): self
    {
        if ($this->controls->removeElement($control)) {
            // set the owning side to null (unless already changed)
            if ($control->getUser() === $this) {
                $control->setUser(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, SafesMovements>
     */
    public function getSafesMovements(): Collection
    {
        return $this->safesMovements;
    }

    /**
     * @param SafesMovements $safesMovement
     * @return $this
     */
    public function addSafesMovement(SafesMovements $safesMovement): self
    {
        if (!$this->safesMovements->contains($safesMovement)) {
            $this->safesMovements->add($safesMovement);
            $safesMovement->setUser($this);
        }

        return $this;
    }

    /**
     * @param SafesMovements $safesMovement
     * @return $this
     */
    public function removeSafesMovement(SafesMovements $safesMovement): self
    {
        if ($this->safesMovements->removeElement($safesMovement)) {
            // set the owning side to null (unless already changed)
            if ($safesMovement->getUser() === $this) {
                $safesMovement->setUser(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, SafesControls>
     */
    public function getSafesControls(): Collection
    {
        return $this->safesControls;
    }

    /**
     * @param SafesControls $safesControl
     * @return $this
     */
    public function addSafesControl(SafesControls $safesControl): self
    {
        if (!$this->safesControls->contains($safesControl)) {
            $this->safesControls->add($safesControl);
            $safesControl->setUser($this);
        }

        return $this;
    }

    /**
     * @param SafesControls $safesControl
     * @return $this
     */
    public function removeSafesControl(SafesControls $safesControl): self
    {
        if ($this->safesControls->removeElement($safesControl)) {
            // set the owning side to null (unless already changed)
            if ($safesControl->getUser() === $this) {
                $safesControl->setUser(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Buybacks>
     */
    public function getBuybacks(): Collection
    {
        return $this->buybacks;
    }

    /**
     * @param Buybacks $buyback
     * @return $this
     */
    public function addBuyback(Buybacks $buyback): self
    {
        if (!$this->buybacks->contains($buyback)) {
            $this->buybacks->add($buyback);
            $buyback->setUser($this);
        }

        return $this;
    }

    /**
     * @param Buybacks $buyback
     * @return $this
     */
    public function removeBuyback(Buybacks $buyback): self
    {
        if ($this->buybacks->removeElement($buyback)) {
            // set the owning side to null (unless already changed)
            if ($buyback->getUser() === $this) {
                $buyback->setUser(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, DepositsSales>
     */
    public function getDepositsSales(): Collection
    {
        return $this->depositsSales;
    }

    /**
     * @param DepositsSales $depositsSale
     * @return $this
     */
    public function addDepositsSale(DepositsSales $depositsSale): self
    {
        if (!$this->depositsSales->contains($depositsSale)) {
            $this->depositsSales->add($depositsSale);
            $depositsSale->setUser($this);
        }

        return $this;
    }

    /**
     * @param DepositsSales $depositsSale
     * @return $this
     */
    public function removeDepositsSale(DepositsSales $depositsSale): self
    {
        if ($this->depositsSales->removeElement($depositsSale)) {
            // set the owning side to null (unless already changed)
            if ($depositsSale->getUser() === $this) {
                $depositsSale->setUser(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, AdvancesPayments>
     */
    public function getAdvancesPayments(): Collection
    {
        return $this->advancesPayments;
    }

    public function addAdvancesPayment(AdvancesPayments $advancesPayment): self
    {
        if (!$this->advancesPayments->contains($advancesPayment)) {
            $this->advancesPayments->add($advancesPayment);
            $advancesPayment->setUser($this);
        }

        return $this;
    }

    public function removeAdvancesPayment(AdvancesPayments $advancesPayment): self
    {
        if ($this->advancesPayments->removeElement($advancesPayment)) {
            // set the owning side to null (unless already changed)
            if ($advancesPayment->getUser() === $this) {
                $advancesPayment->setUser(null);
            }
        }

        return $this;
    }
}
