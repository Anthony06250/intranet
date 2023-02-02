<?php

namespace App\Entity;

use App\Repository\StoresRepository;
use App\Trait\TimeStampTrait;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use libphonenumber\PhoneNumber;
use Symfony\Component\Validator\Constraints as Assert;
use Misd\PhoneNumberBundle\Validator\Constraints\PhoneNumber as AssertPhoneNumber;

#[ORM\Entity(repositoryClass: StoresRepository::class)]
#[ORM\HasLifecycleCallbacks]
class Stores
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
    #[ORM\Column(length: 255, nullable: true)]
    #[Assert\Length(max: 255)]
    private ?string $address = null;

    /**
     * @var string|null
     */
    #[ORM\Column(length: 255, nullable: true)]
    #[Assert\Length(max: 255)]
    private ?string $additional_address = null;

    /**
     * @var string|null
     */
    #[ORM\Column(length: 50, nullable: false)]
    #[Assert\NotBlank]
    #[Assert\Length(min:2, max: 50)]
    private ?string $city = null;

    /**
     * @var string|null
     */
    #[ORM\Column(length: 10, nullable: true)]
    #[Assert\Length(max: 10)]
    private ?string $zipcode = null;

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
    #[Assert\Length(max: 180)]
    private ?string $email = null;

    /**
     * @var string|null
     */
    #[ORM\Column(length: 50, nullable: true)]
    #[Assert\Length(max: 50)]
    private ?string $plus_code = null;

    /**
     * @var string|null
     */
    #[ORM\Column(length: 20, nullable: true)]
    #[Assert\Length(max: 20)]
    private ?string $commercial_register_number = null;

    /**
     * @var ArrayCollection|Collection
     */
    #[ORM\ManyToMany(targetEntity: Users::class, mappedBy: 'stores')]
    private Collection|ArrayCollection $users;

    /**
     * @var ArrayCollection|Collection
     */
    #[ORM\OneToMany(mappedBy: 'store', targetEntity: Controls::class)]
    private Collection|ArrayCollection $controls;

    /**
     * @var ArrayCollection|Collection
     */
    #[ORM\OneToMany(mappedBy: 'store', targetEntity: Safes::class)]
    private Collection|ArrayCollection $safes;

    /**
     * @var ArrayCollection|Collection
     */
    #[ORM\OneToMany(mappedBy: 'store', targetEntity: SafesMovements::class)]
    private Collection|ArrayCollection $safesMovements;

    /**
     * @var ArrayCollection|Collection
     */
    #[ORM\OneToMany(mappedBy: 'store', targetEntity: SafesControls::class)]
    private Collection|ArrayCollection $safesControls;

    /**
     * @var ArrayCollection|Collection
     */
    #[ORM\OneToMany(mappedBy: 'store', targetEntity: Buybacks::class)]
    private Collection|ArrayCollection $buybacks;

    /**
     * @var ArrayCollection|Collection
     */
    #[ORM\OneToMany(mappedBy: 'store', targetEntity: DepositsSales::class)]
    private Collection|ArrayCollection $depositsSales;

    public function __construct()
    {
        $this->users = new ArrayCollection();
        $this->controls = new ArrayCollection();
        $this->safes = new ArrayCollection();
        $this->safesMovements = new ArrayCollection();
        $this->safesControls = new ArrayCollection();
        $this->buybacks = new ArrayCollection();
        $this->depositsSales = new ArrayCollection();
    }

    /**
     * @return string
     */
    public function __toString(): string
    {
        return $this->getCity();
    }

    /**
     * @param $property
     * @return mixed
     */
    public function __get($property): mixed
    {
        return $this->$property;
    }

    /**
     * @param $property
     * @return bool
     */
    public function __isset($property): bool
    {
        return isset($this->$property);
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
    public function getAddress(): ?string
    {
        return $this->address ?: null;
    }

    /**
     * @param string|null $address
     * @return $this
     */
    public function setAddress(?string $address): self
    {
        $this->address = $address;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getAdditionalAddress(): ?string
    {
        return $this->additional_address ?: null;
    }

    /**
     * @param string|null $additional_address
     * @return $this
     */
    public function setAdditionalAddress(?string $additional_address): self
    {
        $this->additional_address = $additional_address;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getCity(): ?string
    {
        return $this->city ? ucfirst($this->city) : null;
    }

    /**
     * @param string $city
     * @return $this
     */
    public function setCity(string $city): self
    {
        $this->city = strtolower($city);

        return $this;
    }

    /**
     * @return string|null
     */
    public function getZipcode(): ?string
    {
        return $this->zipcode ? strtoupper($this->zipcode) : null;
    }

    /**
     * @param string|null $zipcode
     * @return $this
     */
    public function setZipcode(?string $zipcode): self
    {
        $this->zipcode = strtolower($zipcode);

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
     * @return string|null
     */
    public function getPlusCode(): ?string
    {
        return $this->plus_code;
    }

    /**
     * @param string|null $plus_code
     * @return $this
     */
    public function setPlusCode(?string $plus_code): self
    {
        $this->plus_code = $plus_code;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getCommercialRegisterNumber(): ?string
    {
        return $this->commercial_register_number;
    }

    /**
     * @param string|null $commercial_register_number
     * @return $this
     */
    public function setCommercialRegisterNumber(?string $commercial_register_number): self
    {
        $this->commercial_register_number = $commercial_register_number;

        return $this;
    }

    /**
     * @return Collection<int, Users>
     */
    public function getUsers(): Collection
    {
        return $this->users;
    }

    /**
     * @param Users $user
     * @return $this
     */
    public function addUser(Users $user): self
    {
        if (!$this->users->contains($user)) {
            $this->users->add($user);
            $user->addStore($this);
        }

        return $this;
    }

    /**
     * @param Users $user
     * @return $this
     */
    public function removeUser(Users $user): self
    {
        if ($this->users->removeElement($user)) {
            $user->removeStore($this);
        }

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
            $control->setStore($this);
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
            if ($control->getStore() === $this) {
                $control->setStore(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Safes>
     */
    public function getSafes(): Collection
    {
        return $this->safes;
    }

    /**
     * @param Safes $safe
     * @return $this
     */
    public function addSafe(Safes $safe): self
    {
        if (!$this->safes->contains($safe)) {
            $this->safes->add($safe);
            $safe->setStore($this);
        }

        return $this;
    }

    /**
     * @param Safes $safe
     * @return $this
     */
    public function removeSafe(Safes $safe): self
    {
        if ($this->safes->removeElement($safe)) {
            // set the owning side to null (unless already changed)
            if ($safe->getStore() === $this) {
                $safe->setStore(null);
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
            $safesMovement->setStore($this);
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
            if ($safesMovement->getStore() === $this) {
                $safesMovement->setStore(null);
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
            $safesControl->setStore($this);
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
            if ($safesControl->getStore() === $this) {
                $safesControl->setStore(null);
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
            $buyback->setStore($this);
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
            if ($buyback->getStore() === $this) {
                $buyback->setStore(null);
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
            $depositsSale->setStore($this);
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
            if ($depositsSale->getStore() === $this) {
                $depositsSale->setStore(null);
            }
        }

        return $this;
    }
}
