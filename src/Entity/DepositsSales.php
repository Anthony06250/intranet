<?php

namespace App\Entity;

use App\DBAL\Types\DepositsSalesStatusesType;
use App\Repository\DepositsSalesRepository;
use App\Trait\TimeStampTrait;
use DateTimeImmutable;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Fresh\DoctrineEnumBundle\Validator\Constraints\EnumType;
use IntlDateFormatter;
use Locale;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: DepositsSalesRepository::class)]
#[ORM\HasLifecycleCallbacks]
class DepositsSales
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
    #[ORM\ManyToOne(inversedBy: 'depositsSales')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Users $user = null;

    /**
     * @var Stores|null
     */
    #[ORM\ManyToOne(inversedBy: 'depositsSales')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Stores $store = null;

    /**
     * @var string|null
     */
    #[ORM\Column(length: 255)]
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
     * @var string
     */
    #[ORM\Column(name: 'depositsSalesStatus', type: 'DepositsSalesStatusesType', nullable: false)]
    #[EnumType(entity: DepositsSalesStatusesType::class)]
    private string $status = DepositsSalesStatusesType::STATE_PENDING;

    /**
     * @var float|null
     */
    #[ORM\Column(nullable: false)]
    private ?float $reservedPrice = null;

    /**
     * @var Customers|null
     */
    #[ORM\ManyToOne(inversedBy: 'depositsSales')]
    #[ORM\JoinColumn(nullable: true)]
    private ?Customers $customer = null;

    /**
     * @var string|null
     */
    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $comments = null;

    public function __construct()
    {
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
     * @return string
     */
    public function getStatus(): string
    {
        return $this->status;
    }

    /**
     * @param string $status
     * @return $this
     */
    public function setStatus(string $status): self
    {
        $this->status = $status;

        return $this;
    }

    /**
     * @return float|null
     */
    public function getReservedPrice(): ?float
    {
        return $this->reservedPrice;
    }

    /**
     * @param float $reservedPrice
     * @return $this
     */
    public function setReservedPrice(float $reservedPrice): self
    {
        $this->reservedPrice = $reservedPrice;

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
     * @return string|null
     */
    public function getComments(): ?string
    {
        return $this->comments;
    }

    /**
     * @param string|null $comments
     * @return $this
     */
    public function setComments(?string $comments): self
    {
        $this->comments = $comments;

        return $this;
    }
}
