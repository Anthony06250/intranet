<?php

namespace App\Entity;

use App\Repository\CustomersRepository;
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

#[UniqueEntity('id_number')]
#[ORM\Entity(repositoryClass: CustomersRepository::class)]
#[ORM\HasLifecycleCallbacks]
class Customers
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
     * @var UsersCivilities|null
     */
    #[ORM\ManyToOne(inversedBy: 'customers')]
    #[ORM\JoinColumn(nullable: true)]
    private ?UsersCivilities $civility = null;

    /**
     * @var string|null
     */
    #[ORM\Column(length: 50, nullable: true)]
    #[Assert\NotBlank]
    #[Assert\Length(min: 2, max: 50)]
    private ?string $firstname = null;

    /**
     * @var string|null
     */
    #[ORM\Column(length: 50, nullable: true)]
    #[Assert\NotBlank]
    #[Assert\Length(min: 2, max: 50)]
    private ?string $lastname = null;

    /**
     * @var DateTimeInterface|null
     */
    #[ORM\Column(type: Types::DATETIME_MUTABLE, nullable: true)]
    private ?DateTimeInterface $birthday_date = null;

    /**
     * @var CustomersTypesIds|null
     */
    #[ORM\ManyToOne(inversedBy: 'customers')]
    #[ORM\JoinColumn(nullable: true)]
    private ?CustomersTypesIds $customersTypesId = null;

    /**
     * @var string|null
     */
    #[ORM\Column(length: 20, nullable: true)]
    #[Assert\Length(max: 20)]
    private ?string $id_number = null;

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
    #[ORM\Column(length: 50, nullable: true)]
    #[Assert\Length(max: 50)]
    private ?string $city = null;

    /**
     * @var string|null
     */
    #[ORM\Column(length: 20, nullable: true)]
    #[Assert\Length(max: 20)]
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
     * @var ArrayCollection|Collection
     */
    #[ORM\OneToMany(mappedBy: 'customer', targetEntity: Buybacks::class)]
    private Collection|ArrayCollection $buybacks;

    /**
     * @var ArrayCollection|Collection
     */
    #[ORM\OneToMany(mappedBy: 'customer', targetEntity: DepositsSales::class)]
    private Collection|ArrayCollection $depositsSales;

    #[ORM\OneToMany(mappedBy: 'customer', targetEntity: AdvancesPayments::class)]
    private Collection $advancesPayments;

    #[ORM\OneToMany(mappedBy: 'customer', targetEntity: Invoices::class)]
    private Collection $invoices;

    public function __construct()
    {
        $this->buybacks = new ArrayCollection();
        $this->depositsSales = new ArrayCollection();
        $this->advancesPayments = new ArrayCollection();
        $this->invoices = new ArrayCollection();
    }

    /**
     * @return string
     */
    public function __toString(): string
    {
        return $this->getFullname();
    }

    /**
     * @return int|null
     */
    public function getId(): ?int
    {
        return $this->id;
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
     * @param string $firstname
     * @return $this
     */
    public function setFirstname(string $firstname): self
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
     * @param string $lastname
     * @return $this
     */
    public function setLastname(string $lastname): self
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
     * @return CustomersTypesIds|null
     */
    public function getCustomersTypesId(): ?CustomersTypesIds
    {
        return $this->customersTypesId;
    }

    /**
     * @param CustomersTypesIds|null $customersTypesId
     * @return $this
     */
    public function setCustomersTypesId(?CustomersTypesIds $customersTypesId): self
    {
        $this->customersTypesId = $customersTypesId;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getIdNumber(): ?string
    {
        return $this->id_number ? strtoupper($this->id_number) : null;
    }

    /**
     * @param string|null $id_number
     * @return $this
     */
    public function setIdNumber(?string $id_number): self
    {
        $this->id_number = strtolower($id_number);

        return $this;
    }

    /**
     * @return string|null
     */
    public function getAddress(): ?string
    {
        return $this->address;
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
        return $this->additional_address;
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
     * @param string|null $city
     * @return $this
     */
    public function setCity(?string $city): self
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
        $this->email = $email;

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
            $buyback->setCustomer($this);
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
            if ($buyback->getCustomer() === $this) {
                $buyback->setCustomer(null);
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
            $depositsSale->setCustomer($this);
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
            if ($depositsSale->getCustomer() === $this) {
                $depositsSale->setCustomer(null);
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
            $advancesPayment->setCustomer($this);
        }

        return $this;
    }

    public function removeAdvancesPayment(AdvancesPayments $advancesPayment): self
    {
        if ($this->advancesPayments->removeElement($advancesPayment)) {
            // set the owning side to null (unless already changed)
            if ($advancesPayment->getCustomer() === $this) {
                $advancesPayment->setCustomer(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Invoices>
     */
    public function getInvoices(): Collection
    {
        return $this->invoices;
    }

    public function addInvoice(Invoices $invoice): self
    {
        if (!$this->invoices->contains($invoice)) {
            $this->invoices->add($invoice);
            $invoice->setCustomer($this);
        }

        return $this;
    }

    public function removeInvoice(Invoices $invoice): self
    {
        if ($this->invoices->removeElement($invoice)) {
            // set the owning side to null (unless already changed)
            if ($invoice->getCustomer() === $this) {
                $invoice->setCustomer(null);
            }
        }

        return $this;
    }
}
