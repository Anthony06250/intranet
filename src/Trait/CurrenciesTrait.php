<?php

namespace App\Trait;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

trait CurrenciesTrait
{
    /**
     * @var int|null
     */
    #[ORM\Column(nullable: true)]
    #[Assert\PositiveOrZero]
    private ?int $one_cent = null;

    /**
     * @var int|null
     */
    #[ORM\Column(nullable: true)]
    #[Assert\PositiveOrZero]
    private ?int $two_cents = null;

    /**
     * @var int|null
     */
    #[ORM\Column(nullable: true)]
    #[Assert\PositiveOrZero]
    private ?int $five_cents = null;

    /**
     * @var int|null
     */
    #[ORM\Column(nullable: true)]
    #[Assert\PositiveOrZero]
    private ?int $ten_cents = null;

    /**
     * @var int|null
     */
    #[ORM\Column(nullable: true)]
    #[Assert\PositiveOrZero]
    private ?int $twenty_cents = null;

    /**
     * @var int|null
     */
    #[ORM\Column(nullable: true)]
    #[Assert\PositiveOrZero]
    private ?int $fifty_cents = null;

    /**
     * @var int|null
     */
    #[ORM\Column(nullable: true)]
    #[Assert\PositiveOrZero]
    private ?int $one_euro = null;

    /**
     * @var int|null
     */
    #[ORM\Column(nullable: true)]
    #[Assert\PositiveOrZero]
    private ?int $two_euros = null;

    /**
     * @var int|null
     */
    #[ORM\Column(nullable: true)]
    #[Assert\PositiveOrZero]
    private ?int $five_euros = null;

    /**
     * @var int|null
     */
    #[ORM\Column(nullable: true)]
    #[Assert\PositiveOrZero]
    private ?int $ten_euros = null;

    /**
     * @var int|null
     */
    #[ORM\Column(nullable: true)]
    #[Assert\PositiveOrZero]
    private ?int $twenty_euros = null;

    /**
     * @var int|null
     */
    #[ORM\Column(nullable: true)]
    #[Assert\PositiveOrZero]
    private ?int $fifty_euros = null;

    /**
     * @var int|null
     */
    #[ORM\Column(nullable: true)]
    #[Assert\PositiveOrZero]
    private ?int $one_hundred_euros = null;

    /**
     * @var int|null
     */
    #[ORM\Column(nullable: true)]
    #[Assert\PositiveOrZero]
    private ?int $two_hundred_euros = null;

    /**
     * @var int|null
     */
    #[ORM\Column(nullable: true)]
    #[Assert\PositiveOrZero]
    private ?int $five_hundred_euros = null;

    /**
     * @return int|null
     */
    public function getOneCent(): ?int
    {
        return $this->one_cent;
    }

    /**
     * @param int|null $one_cent
     * @return $this
     */
    public function setOneCent(?int $one_cent): self
    {
        $this->one_cent = $one_cent;

        return $this;
    }

    /**
     * @return int|null
     */
    public function getTwoCents(): ?int
    {
        return $this->two_cents;
    }

    /**
     * @param int|null $two_cents
     * @return $this
     */
    public function setTwoCents(?int $two_cents): self
    {
        $this->two_cents = $two_cents;

        return $this;
    }

    /**
     * @return int|null
     */
    public function getFiveCents(): ?int
    {
        return $this->five_cents;
    }

    /**
     * @param int|null $five_cents
     * @return $this
     */
    public function setFiveCents(?int $five_cents): self
    {
        $this->five_cents = $five_cents;

        return $this;
    }

    /**
     * @return int|null
     */
    public function getTenCents(): ?int
    {
        return $this->ten_cents;
    }

    /**
     * @param int|null $ten_cents
     * @return $this
     */
    public function setTenCents(?int $ten_cents): self
    {
        $this->ten_cents = $ten_cents;

        return $this;
    }

    /**
     * @return int|null
     */
    public function getTwentyCents(): ?int
    {
        return $this->twenty_cents;
    }

    /**
     * @param int|null $twenty_cents
     * @return $this
     */
    public function setTwentyCents(?int $twenty_cents): self
    {
        $this->twenty_cents = $twenty_cents;

        return $this;
    }

    /**
     * @return int|null
     */
    public function getFiftyCents(): ?int
    {
        return $this->fifty_cents;
    }

    /**
     * @param int|null $fifty_cents
     * @return $this
     */
    public function setFiftyCents(?int $fifty_cents): self
    {
        $this->fifty_cents = $fifty_cents;

        return $this;
    }

    /**
     * @return int|null
     */
    public function getOneEuro(): ?int
    {
        return $this->one_euro;
    }

    /**
     * @param int|null $one_euro
     * @return $this
     */
    public function setOneEuro(?int $one_euro): self
    {
        $this->one_euro = $one_euro;

        return $this;
    }

    /**
     * @return int|null
     */
    public function getTwoEuros(): ?int
    {
        return $this->two_euros;
    }

    /**
     * @param int|null $two_euros
     * @return $this
     */
    public function setTwoEuros(?int $two_euros): self
    {
        $this->two_euros = $two_euros;

        return $this;
    }

    /**
     * @return int|null
     */
    public function getFiveEuros(): ?int
    {
        return $this->five_euros;
    }

    /**
     * @param int|null $five_euros
     * @return $this
     */
    public function setFiveEuros(?int $five_euros): self
    {
        $this->five_euros = $five_euros;

        return $this;
    }

    /**
     * @return int|null
     */
    public function getTenEuros(): ?int
    {
        return $this->ten_euros;
    }

    /**
     * @param int|null $ten_euros
     * @return $this
     */
    public function setTenEuros(?int $ten_euros): self
    {
        $this->ten_euros = $ten_euros;

        return $this;
    }

    /**
     * @return int|null
     */
    public function getTwentyEuros(): ?int
    {
        return $this->twenty_euros;
    }

    /**
     * @param int|null $twenty_euros
     * @return $this
     */
    public function setTwentyEuros(?int $twenty_euros): self
    {
        $this->twenty_euros = $twenty_euros;

        return $this;
    }

    /**
     * @return int|null
     */
    public function getFiftyEuros(): ?int
    {
        return $this->fifty_euros;
    }

    /**
     * @param int|null $fifty_euros
     * @return $this
     */
    public function setFiftyEuros(?int $fifty_euros): self
    {
        $this->fifty_euros = $fifty_euros;

        return $this;
    }

    /**
     * @return int|null
     */
    public function getOneHundredEuros(): ?int
    {
        return $this->one_hundred_euros;
    }

    /**
     * @param int|null $one_hundred_euros
     * @return $this
     */
    public function setOneHundredEuros(?int $one_hundred_euros): self
    {
        $this->one_hundred_euros = $one_hundred_euros;

        return $this;
    }

    /**
     * @return int|null
     */
    public function getTwoHundredEuros(): ?int
    {
        return $this->two_hundred_euros;
    }

    /**
     * @param int|null $two_hundred_euros
     * @return $this
     */
    public function setTwoHundredEuros(?int $two_hundred_euros): self
    {
        $this->two_hundred_euros = $two_hundred_euros;

        return $this;
    }

    /**
     * @return int|null
     */
    public function getFiveHundredEuros(): ?int
    {
        return $this->five_hundred_euros;
    }

    /**
     * @param int|null $five_hundred_euros
     * @return $this
     */
    public function setFiveHundredEuros(?int $five_hundred_euros): self
    {
        $this->five_hundred_euros = $five_hundred_euros;

        return $this;
    }
}