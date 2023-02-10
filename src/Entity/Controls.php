<?php

namespace App\Entity;

use App\Repository\ControlsRepository;
use App\Trait\CurrenciesTrait;
use App\Trait\TimeStampTrait;
use DateTimeImmutable;
use Doctrine\ORM\Mapping as ORM;
use IntlDateFormatter;
use Locale;

#[ORM\Entity(repositoryClass: ControlsRepository::class)]
#[ORM\HasLifecycleCallbacks]
class Controls
{
    use CurrenciesTrait;
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
    #[ORM\ManyToOne(inversedBy: 'controls')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Users $user = null;

    /**
     * @var Stores|null
     */
    #[ORM\ManyToOne(inversedBy: 'controls')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Stores $store = null;

    /**
     * @var ControlsCounters|null
     */
    #[ORM\ManyToOne(inversedBy: 'controls')]
    #[ORM\JoinColumn(nullable: false)]
    private ?ControlsCounters $counter = null;

    /**
     * @var ControlsPeriods|null
     */
    #[ORM\ManyToOne(inversedBy: 'controls')]
    #[ORM\JoinColumn(nullable: false)]
    private ?ControlsPeriods $period = null;

    /**
     * @var float|null
     */
    #[ORM\Column(nullable: false)]
    private ?float $turnover = null;

    /**
     * @var float|null
     */
    #[ORM\Column(nullable: false)]
    private ?float $cashFund = null;

    /**
     * @var float|null
     */
    #[ORM\Column(nullable: false)]
    private ?float $result = null;

    /**
     * @var float|null
     */
    #[ORM\Column(nullable: false)]
    private ?float $error = null;

    public function __construct()
    {
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

        return $this->getStore() . ' - (' . $this->getCounter() . ' / ' . $this->getPeriod() . ') - '
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
     * @return ControlsCounters|null
     */
    public function getCounter(): ?ControlsCounters
    {
        return $this->counter;
    }

    /**
     * @param ControlsCounters|null $counter
     * @return $this
     */
    public function setCounter(?ControlsCounters $counter): self
    {
        $this->counter = $counter;

        return $this;
    }

    /**
     * @return ControlsPeriods|null
     */
    public function getPeriod(): ?ControlsPeriods
    {
        return $this->period;
    }

    /**
     * @param ControlsPeriods|null $period
     * @return $this
     */
    public function setPeriod(?ControlsPeriods $period): self
    {
        $this->period = $period;

        return $this;
    }

    /**
     * @return float|null
     */
    public function getTurnover(): ?float
    {
        return $this->turnover;
    }

    /**
     * @param float $turnover
     * @return $this
     */
    public function setTurnover(float $turnover): self
    {
        $this->turnover = $turnover;

        return $this;
    }

    /**
     * @return float|null
     */
    public function getCashFund(): ?float
    {
        return $this->cashFund;
    }

    /**
     * @param float $cashFund
     * @return $this
     */
    public function setCashFund(float $cashFund): self
    {
        $this->cashFund = $cashFund;

        return $this;
    }

    /**
     * @return float|null
     */
    public function getResult(): ?float
    {
        return $this->result;
    }

    /**
     * @param float $result
     * @return $this
     */
    public function setResult(float $result): self
    {
        $this->result = $result;

        return $this;
    }

    /**
     * @return float|null
     */
    public function getError(): ?float
    {
        return $this->error;
    }

    /**
     * @param float $error
     * @return $this
     */
    public function setError(float $error): self
    {
        $this->error = $error;

        return $this;
    }
}
