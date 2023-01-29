<?php

namespace App\Entity;

use App\Repository\SafesMovementsRepository;
use App\Trait\TimeStampTrait;
use DateTimeImmutable;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use IntlDateFormatter;
use Locale;

#[ORM\Entity(repositoryClass: SafesMovementsRepository::class)]
#[ORM\HasLifecycleCallbacks]
class SafesMovements
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
    #[ORM\ManyToOne(inversedBy: 'safesMovements')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Users $user = null;

    /**
     * @var Stores|null
     */
    #[ORM\ManyToOne(inversedBy: 'safesMovements')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Stores $store = null;

    /**
     * @var float|null
     */
    #[ORM\Column(nullable: false)]
    private ?float $amount = null;

    /**
     * @var SafesMovementsTypes|null
     */
    #[ORM\ManyToOne(inversedBy: 'safesMovements')]
    #[ORM\JoinColumn(nullable: false)]
    private ?SafesMovementsTypes $safesMovementsType = null;

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

        return $this->getStore() . ' - (' . $this->getSafesMovementsType() . ') - '
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
     * @return float|null
     */
    public function getAmount(): ?float
    {
        return $this->amount;
    }

    /**
     * @param float $amount
     * @return $this
     */
    public function setAmount(float $amount): self
    {
        $this->amount = $amount;

        return $this;
    }

    /**
     * @return SafesMovementsTypes|null
     */
    public function getSafesMovementsType(): ?SafesMovementsTypes
    {
        return $this->safesMovementsType;
    }

    /**
     * @param SafesMovementsTypes|null $safesMovementsType
     * @return $this
     */
    public function setSafesMovementsType(?SafesMovementsTypes $safesMovementsType): self
    {
        $this->safesMovementsType = $safesMovementsType;

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
