<?php

namespace App\Entity;

use App\DBAL\Types\AdvancesPaymentsStatusesType;
use App\Repository\AdvancesPaymentsRepository;
use App\Trait\TimeStampTrait;
use DateTimeImmutable;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Fresh\DoctrineEnumBundle\Validator\Constraints\EnumType;
use IntlDateFormatter;
use Locale;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Validator\Constraints as Assert;

#[UniqueEntity('barCode')]
#[ORM\Entity(repositoryClass: AdvancesPaymentsRepository::class)]
#[ORM\HasLifecycleCallbacks]
class AdvancesPayments
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
    #[ORM\ManyToOne(inversedBy: 'advancesPayments')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Users $user = null;

    /**
     * @var Stores|null
     */
    #[ORM\ManyToOne(inversedBy: 'advancesPayments')]
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
    #[ORM\Column(length: 20, nullable: false)]
    #[Assert\NotBlank]
    #[Assert\Length(min:2, max: 20)]
    private ?string $barCode = null;

    /**
     * @var string
     */
    #[ORM\Column(name: 'advancesPaymentsStatus', type: 'AdvancesPaymentsStatusesType', nullable: false)]
    #[EnumType(entity: AdvancesPaymentsStatusesType::class)]
    private string $status = AdvancesPaymentsStatusesType::STATE_PENDING;

    /**
     * @var float|null
     */
    #[ORM\Column(nullable: false)]
    private ?float $depositAmount = null;

    /**
     * @var DateTimeImmutable|null
     */
    #[ORM\Column(nullable: false)]
    private ?DateTimeImmutable $expiredAt = null;

    /**
     * @var Customers|null
     */
    #[ORM\ManyToOne(inversedBy: 'advancesPayments')]
    #[ORM\JoinColumn(nullable: true)]
    private ?Customers $customer = null;

    /**
     * @var string|null
     */
    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $comments = null;

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
    public function getBarCode(): ?string
    {
        return $this->barCode;
    }

    /**
     * @param string $barCode
     * @return $this
     */
    public function setBarCode(string $barCode): self
    {
        $this->barCode = $barCode;

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
    public function getDepositAmount(): ?float
    {
        return $this->depositAmount;
    }

    /**
     * @param float $depositAmount
     * @return $this
     */
    public function setDepositAmount(float $depositAmount): self
    {
        $this->depositAmount = $depositAmount;

        return $this;
    }

    /**
     * @return DateTimeImmutable|null
     */
    public function getExpiredAt(): ?DateTimeImmutable
    {
        return $this->expiredAt;
    }

    /**
     * @param DateTimeImmutable $expiredAt
     * @return $this
     */
    public function setExpiredAt(DateTimeImmutable $expiredAt): self
    {
        $this->expiredAt = $expiredAt;

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
