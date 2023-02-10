<?php

namespace App\Entity;

use App\Repository\InvoicesRepository;
use App\Trait\TimeStampTrait;
use DateTimeImmutable;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use IntlDateFormatter;
use Locale;

#[ORM\Entity(repositoryClass: InvoicesRepository::class)]
#[ORM\HasLifecycleCallbacks]
class Invoices
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
     * @var Users|null
     */
    #[ORM\ManyToOne(inversedBy: 'invoices')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Users $user = null;

    /**
     * @var Stores|null
     */
    #[ORM\ManyToOne(inversedBy: 'invoices')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Stores $store = null;

    /**
     * @var ArrayCollection|Collection
     */
    #[ORM\ManyToMany(targetEntity: Products::class, inversedBy: 'invoices', cascade: ["persist"])]
    #[ORM\JoinColumn(nullable: false)]
    private Collection|ArrayCollection $products;

    /**
     * @var float|null
     */
    #[ORM\Column(nullable: false)]
    private ?float $totalWithoutTaxes = null;

    /**
     * @var InvoicesTaxesRates|null
     */
    #[ORM\ManyToOne(inversedBy: 'invoices')]
    #[ORM\JoinColumn(nullable: false)]
    private ?InvoicesTaxesRates $taxesRate = null;

    /**
     * @var float|null
     */
    #[ORM\Column(nullable: false)]
    private ?float $totalWithTaxes = null;

    /**
     * @var float|null
     */
    #[ORM\Column(nullable: true)]
    private ?float $taxesAmount = null;

    /**
     * @var Customers|null
     */
    #[ORM\ManyToOne(cascade: ["persist"], inversedBy: 'invoices')]
    #[ORM\JoinColumn(nullable: true)]
    private ?Customers $customer = null;

    /**
     * @var DateTimeImmutable|null
     */
    #[ORM\Column(nullable: false)]
    private ?DateTimeImmutable $selledAt;

    /**
     * @var AdvancesPaymentsMethods|null
     */
    #[ORM\ManyToOne(inversedBy: 'invoices')]
    #[ORM\JoinColumn(nullable: false)]
    private ?AdvancesPaymentsMethods $paymentsMethod = null;

    public function __construct()
    {
        $this->products = new ArrayCollection();
        $this->selledAt = new DateTimeImmutable();
        $this->createdAt = new DateTimeImmutable();
    }

    /**
     * @return string
     */
    public function __toString(): string
    {
        $dateFormatter = IntlDateFormatter::create(
            Locale::getDefault(),
            IntlDateFormatter::FULL,
            IntlDateFormatter::NONE,
            null,
            IntlDateFormatter::GREGORIAN,
        );

        return $this->getStore() . ' - (' . $this->getUser() . ') - '
            . $dateFormatter->format($this->getCreatedAt()->getTimestamp());
    }

    /**
     * @return int|null
     */
    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * @return Users|null
     */
    public function getUser(): ?Users
    {
        return $this->user;
    }

    /**
     * @param Users|null $user
     * @return $this
     */
    public function setUser(?Users $user): self
    {
        $this->user = $user;

        return $this;
    }

    /**
     * @return Stores|null
     */
    public function getStore(): ?Stores
    {
        return $this->store;
    }

    /**
     * @param Stores|null $store
     * @return $this
     */
    public function setStore(?Stores $store): self
    {
        $this->store = $store;

        return $this;
    }

    /**
     * @return Collection<int, Products>
     */
    public function getProducts(): Collection
    {
        return $this->products;
    }

    /**
     * @param Products $product
     * @return $this
     */
    public function addProduct(Products $product): self
    {
        if (!$this->products->contains($product)) {
            $this->products->add($product);
        }

        return $this;
    }

    /**
     * @param Products $product
     * @return $this
     */
    public function removeProduct(Products $product): self
    {
        $this->products->removeElement($product);

        return $this;
    }

    /**
     * @return float|null
     */
    public function getTotalWithoutTaxes(): ?float
    {
        return $this->totalWithoutTaxes;
    }

    /**
     * @param float $totalWithoutTaxes
     * @return $this
     */
    public function setTotalWithoutTaxes(float $totalWithoutTaxes): self
    {
        $this->totalWithoutTaxes = $totalWithoutTaxes;

        return $this;
    }

    /**
     * @return InvoicesTaxesRates|null
     */
    public function getTaxesRate(): ?InvoicesTaxesRates
    {
        return $this->taxesRate;
    }

    /**
     * @param InvoicesTaxesRates|null $taxesRate
     * @return $this
     */
    public function setTaxesRate(?InvoicesTaxesRates $taxesRate): self
    {
        $this->taxesRate = $taxesRate;

        return $this;
    }

    /**
     * @return float|null
     */
    public function getTotalWithTaxes(): ?float
    {
        return $this->totalWithTaxes;
    }

    /**
     * @param float $totalWithTaxes
     * @return $this
     */
    public function setTotalWithTaxes(float $totalWithTaxes): self
    {
        $this->totalWithTaxes = $totalWithTaxes;

        return $this;
    }

    /**
     * @return float|null
     */
    public function getTaxesAmount(): ?float
    {
        return $this->taxesAmount;
    }

    /**
     * @param float|null $taxesAmount
     * @return $this
     */
    public function setTaxesAmount(?float $taxesAmount): self
    {
        $this->taxesAmount = $taxesAmount;

        return $this;
    }

    /**
     * @return Customers|null
     */
    public function getCustomer(): ?Customers
    {
        return $this->customer;
    }

    /**
     * @param Customers|null $customer
     * @return $this
     */
    public function setCustomer(?Customers $customer): self
    {
        $this->customer = $customer;

        return $this;
    }

    /**
     * @return DateTimeImmutable|null
     */
    public function getSelledAt(): ?DateTimeImmutable
    {
        return $this->selledAt;
    }

    /**
     * @param DateTimeImmutable $selledAt
     * @return $this
     */
    public function setSelledAt(DateTimeImmutable $selledAt): self
    {
        $this->selledAt = $selledAt;

        return $this;
    }

    /**
     * @return AdvancesPaymentsMethods|null
     */
    public function getPaymentsMethod(): ?AdvancesPaymentsMethods
    {
        return $this->paymentsMethod;
    }

    /**
     * @param AdvancesPaymentsMethods|null $paymentsMethod
     * @return $this
     */
    public function setPaymentsMethod(?AdvancesPaymentsMethods $paymentsMethod): self
    {
        $this->paymentsMethod = $paymentsMethod;

        return $this;
    }
}
