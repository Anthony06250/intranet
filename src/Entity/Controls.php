<?php

namespace App\Entity;

use App\Repository\ControlsRepository;
use App\Trait\CurrenciesTrait;
use App\Trait\TimeStampTrait;
use DateTimeImmutable;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
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
    private ?ControlsCounters $controlsCounter = null;

    /**
     * @var ControlsPeriods|null
     */
    #[ORM\ManyToOne(inversedBy: 'controls')]
    #[ORM\JoinColumn(nullable: false)]
    private ?ControlsPeriods $controlsPeriod = null;

    /**
     * @var float|null
     */
    #[ORM\Column(nullable: false)]
    private ?float $turnover = null;

    /**
     * @var float|null
     */
    #[ORM\Column(nullable: false)]
    private ?float $cash_fund = null;

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

        return $this->getStore() . ' - (' . $this->getControlsCounter() . ' / ' . $this->getControlsPeriod() . ') - '
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
    public function getControlsCounter(): ?ControlsCounters
    {
        return $this->controlsCounter;
    }

    /**
     * @param ControlsCounters|null $controlsCounter
     * @return $this
     */
    public function setControlsCounter(?ControlsCounters $controlsCounter): self
    {
        $this->controlsCounter = $controlsCounter;

        return $this;
    }

    /**
     * @return ControlsPeriods|null
     */
    public function getControlsPeriod(): ?ControlsPeriods
    {
        return $this->controlsPeriod;
    }

    /**
     * @param ControlsPeriods|null $controlsPeriod
     * @return $this
     */
    public function setControlsPeriod(?ControlsPeriods $controlsPeriod): self
    {
        $this->controlsPeriod = $controlsPeriod;

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
        return $this->cash_fund;
    }

    /**
     * @param float $cash_fund
     * @return $this
     */
    public function setCashFund(float $cash_fund): self
    {
        $this->cash_fund = $cash_fund;

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
