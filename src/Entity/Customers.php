<?php

namespace App\Entity;

use App\Repository\CustomersRepository;
use App\Trait\AddressTrait;
use App\Trait\ContactTrait;
use App\Trait\TimeStampTrait;
use DateTimeInterface;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Validator\Constraints as Assert;

#[UniqueEntity('idNumber')]
#[ORM\Entity(repositoryClass: CustomersRepository::class)]
#[ORM\HasLifecycleCallbacks]
class Customers
{
    use AddressTrait;
    use ContactTrait;
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
    private ?DateTimeInterface $birthdayDate = null;

    /**
     * @var CustomersTypesIds|null
     */
    #[ORM\ManyToOne(inversedBy: 'customers')]
    #[ORM\JoinColumn(nullable: true)]
    private ?CustomersTypesIds $typesId = null;

    /**
     * @var string|null
     */
    #[ORM\Column(length: 20, nullable: true)]
    #[Assert\Length(max: 20)]
    private ?string $idNumber = null;

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
        return $this->birthdayDate;
    }

    /**
     * @param DateTimeInterface|null $birthdayDate
     * @return $this
     */
    public function setBirthdayDate(?DateTimeInterface $birthdayDate): self
    {
        $this->birthdayDate = $birthdayDate;

        return $this;
    }

    /**
     * @return CustomersTypesIds|null
     */
    public function getTypesId(): ?CustomersTypesIds
    {
        return $this->typesId;
    }

    /**
     * @param CustomersTypesIds|null $typesId
     * @return $this
     */
    public function setTypesId(?CustomersTypesIds $typesId): self
    {
        $this->typesId = $typesId;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getIdNumber(): ?string
    {
        return $this->idNumber ? strtoupper($this->idNumber) : null;
    }

    /**
     * @param string|null $idNumber
     * @return $this
     */
    public function setIdNumber(?string $idNumber): self
    {
        $this->idNumber = strtolower($idNumber);

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
