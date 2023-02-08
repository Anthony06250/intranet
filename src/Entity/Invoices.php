<?php

namespace App\Entity;

use App\Repository\InvoicesRepository;
use App\Trait\TimeStampTrait;
use DateTimeImmutable;
use Doctrine\ORM\Mapping as ORM;
use IntlDateFormatter;
use Locale;
use Symfony\Component\Validator\Constraints as Assert;

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
     * @var string|null
     */
    #[ORM\Column(length: 255, nullable: false)]
    #[Assert\NotBlank]
    #[Assert\Length(min:2, max: 255)]
    private ?string $product = null;

    /**
     * @var string|null
     */
    #[ORM\Column(length: 50, nullable: true)]
    #[Assert\Length(max: 50)]
    private ?string $serialNumber = null;

    /**
     * @var string|null
     */
    #[ORM\Column(length: 20, nullable: true)]
    #[Assert\Length(max: 20)]
    private ?string $barCode = null;

    /**
     * @var float|null
     */
    #[ORM\Column(nullable: false)]
    private ?float $productPrice = null;

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
    private ?AdvancesPaymentsMethods $paymentMethods = null;

    public function __construct()
    {
        $this->selledAt = new DateTimeImmutable();
        $this->created_at = new DateTimeImmutable();
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
     * @return string|null
     */
    public function getProduct(): ?string
    {
        return $this->product ? ucfirst($this->product) : null;
    }

    /**
     * @param string $product
     * @return $this
     */
    public function setProduct(string $product): self
    {
        $this->product = strtolower($product);

        return $this;
    }

    /**
     * @return string|null
     */
    public function getSerialNumber(): ?string
    {
        return $this->serialNumber;
    }

    /**
     * @param string|null $serialNumber
     * @return $this
     */
    public function setSerialNumber(?string $serialNumber): self
    {
        $this->serialNumber = $serialNumber;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getBarCode(): ?string
    {
        return $this->barCode;
    }

    /**
     * @param string|null $barCode
     * @return $this
     */
    public function setBarCode(?string $barCode): self
    {
        $this->barCode = $barCode;

        return $this;
    }

    /**
     * @return float|null
     */
    public function getProductPrice(): ?float
    {
        return $this->productPrice;
    }

    /**
     * @param float $productPrice
     * @return $this
     */
    public function setProductPrice(float $productPrice): self
    {
        $this->productPrice = $productPrice;

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
    public function getPaymentMethods(): ?AdvancesPaymentsMethods
    {
        return $this->paymentMethods;
    }

    /**
     * @param AdvancesPaymentsMethods|null $paymentMethods
     * @return $this
     */
    public function setPaymentMethods(?AdvancesPaymentsMethods $paymentMethods): self
    {
        $this->paymentMethods = $paymentMethods;

        return $this;
    }
}
