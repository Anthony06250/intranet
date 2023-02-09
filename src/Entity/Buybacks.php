<?php

namespace App\Entity;

use App\DBAL\Types\BuybacksStatusesType;
use App\Repository\BuybacksRepository;
use App\Trait\TimeStampTrait;
use DateTimeImmutable;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Fresh\DoctrineEnumBundle\Validator\Constraints\EnumType;
use Symfony\Component\Validator\Constraints as Assert;
use IntlDateFormatter;
use Locale;

#[ORM\Entity(repositoryClass: BuybacksRepository::class)]
#[ORM\HasLifecycleCallbacks]
class Buybacks
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
    #[ORM\ManyToOne(inversedBy: 'buybacks')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Users $user = null;

    /**
     * @var Stores|null
     */
    #[ORM\ManyToOne(inversedBy: 'buybacks')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Stores $store = null;

    /**
     * @var ArrayCollection|Collection
     */
    #[ORM\ManyToMany(targetEntity: Products::class, inversedBy: 'buybacks', cascade: ["persist"])]
    #[ORM\JoinColumn(nullable: false)]
    private Collection|ArrayCollection $products;

    /**
     * @var string
     */
    #[ORM\Column(name: 'buybacksStatus', type: 'BuybacksStatusesType', nullable: false)]
    #[EnumType(entity: BuybacksStatusesType::class)]
    private string $status = BuybacksStatusesType::STATE_PENDING;

    /**
     * @var float|null
     */
    #[ORM\Column(nullable: false)]
    private ?float $starting_price = null;

    /**
     * @var float|null
     */
    #[ORM\Column(nullable: false)]
    private ?float $increased_percent = 0.3;

    /**
     * @var float|null
     */
    #[ORM\Column(nullable: false)]
    private ?float $increased_price = null;

    /**
     * @var int|null
     */
    #[ORM\Column(type: Types::SMALLINT, nullable: false)]
    #[Assert\Positive]
    private ?int $duration = 28;

    /**
     * @var DateTimeImmutable|null
     */
    #[ORM\Column(nullable: false)]
    private ?DateTimeImmutable $due_at = null;

    /**
     * @var Customers|null
     */
    #[ORM\ManyToOne(cascade: ["persist"], inversedBy: 'buybacks')]
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
    public function getStartingPrice(): ?float
    {
        return $this->starting_price;
    }

    /**
     * @param float $starting_price
     * @return $this
     */
    public function setStartingPrice(float $starting_price): self
    {
        $this->starting_price = $starting_price;

        return $this;
    }

    /**
     * @return float|null
     */
    public function getIncreasedPercent(): ?float
    {
        return $this->increased_percent;
    }

    /**
     * @param float $increased_percent
     * @return $this
     */
    public function setIncreasedPercent(float $increased_percent): self
    {
        $this->increased_percent = $increased_percent;

        return $this;
    }

    /**
     * @return float|null
     */
    public function getIncreasedPrice(): ?float
    {
        return $this->increased_price;
    }

    /**
     * @param float $increased_price
     * @return $this
     */
    public function setIncreasedPrice(float $increased_price): self
    {
        $this->increased_price = $increased_price;

        return $this;
    }

    /**
     * @return int|null
     */
    public function getDuration(): ?int
    {
        return $this->duration;
    }

    /**
     * @param int $duration
     * @return $this
     */
    public function setDuration(int $duration): self
    {
        $this->duration = $duration;

        return $this;
    }

    /**
     * @return DateTimeImmutable|null
     */
    public function getDueAt(): ?DateTimeImmutable
    {
        return $this->due_at;
    }

    /**
     * @param DateTimeImmutable $due_at
     * @return $this
     */
    public function setDueAt(DateTimeImmutable $due_at): self
    {
        $this->due_at = $due_at;

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
