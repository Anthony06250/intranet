<?php

namespace App\Entity;

use App\DBAL\Types\AdvancesPaymentsStatusesType;
use App\Repository\AdvancesPaymentsRepository;
use App\Trait\TimeStampTrait;
use DateTimeImmutable;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Fresh\DoctrineEnumBundle\Validator\Constraints\EnumType;
use IntlDateFormatter;
use Locale;

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
     * @var ArrayCollection|Collection
     */
    #[ORM\ManyToMany(targetEntity: Products::class, inversedBy: 'advancesPayments', cascade: ["persist"])]
    #[ORM\JoinColumn(nullable: false)]
    private Collection|ArrayCollection $products;

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
     * @var AdvancesPaymentsMethods|null
     */
    #[ORM\ManyToOne(inversedBy: 'advancesPayments')]
    #[ORM\JoinColumn(nullable: false)]
    private ?AdvancesPaymentsMethods $paymentsMethod = null;

    /**
     * @var DateTimeImmutable|null
     */
    #[ORM\Column(nullable: false)]
    private ?DateTimeImmutable $expiredAt = null;

    /**
     * @var Customers|null
     */
    #[ORM\ManyToOne(cascade: ["persist"], inversedBy: 'advancesPayments')]
    #[ORM\JoinColumn(nullable: true)]
    private ?Customers $customer = null;

    /**
     * @var string|null
     */
    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $comments = null;

    public function __construct()
    {
        $this->createdAt = new DateTimeImmutable();
        $this->products = new ArrayCollection();
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
