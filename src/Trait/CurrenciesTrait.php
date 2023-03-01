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
    private ?int $oneCent = null;

    /**
     * @var int|null
     */
    #[ORM\Column(nullable: true)]
    #[Assert\PositiveOrZero]
    private ?int $twoCents = null;

    /**
     * @var int|null
     */
    #[ORM\Column(nullable: true)]
    #[Assert\PositiveOrZero]
    private ?int $fiveCents = null;

    /**
     * @var int|null
     */
    #[ORM\Column(nullable: true)]
    #[Assert\PositiveOrZero]
    private ?int $tenCents = null;

    /**
     * @var int|null
     */
    #[ORM\Column(nullable: true)]
    #[Assert\PositiveOrZero]
    private ?int $twentyCents = null;

    /**
     * @var int|null
     */
    #[ORM\Column(nullable: true)]
    #[Assert\PositiveOrZero]
    private ?int $fiftyCents = null;

    /**
     * @var int|null
     */
    #[ORM\Column(nullable: true)]
    #[Assert\PositiveOrZero]
    private ?int $oneEuro = null;

    /**
     * @var int|null
     */
    #[ORM\Column(nullable: true)]
    #[Assert\PositiveOrZero]
    private ?int $twoEuros = null;

    /**
     * @var int|null
     */
    #[ORM\Column(nullable: true)]
    #[Assert\PositiveOrZero]
    private ?int $fiveEuros = null;

    /**
     * @var int|null
     */
    #[ORM\Column(nullable: true)]
    #[Assert\PositiveOrZero]
    private ?int $tenEuros = null;

    /**
     * @var int|null
     */
    #[ORM\Column(nullable: true)]
    #[Assert\PositiveOrZero]
    private ?int $twentyEuros = null;

    /**
     * @var int|null
     */
    #[ORM\Column(nullable: true)]
    #[Assert\PositiveOrZero]
    private ?int $fiftyEuros = null;

    /**
     * @var int|null
     */
    #[ORM\Column(nullable: true)]
    #[Assert\PositiveOrZero]
    private ?int $oneHundredEuros = null;

    /**
     * @var int|null
     */
    #[ORM\Column(nullable: true)]
    #[Assert\PositiveOrZero]
    private ?int $twoHundredEuros = null;

    /**
     * @var int|null
     */
    #[ORM\Column(nullable: true)]
    #[Assert\PositiveOrZero]
    private ?int $fiveHundredEuros = null;

    /**
     * @return int|null
     */
    public function getOneCent(): ?int
    {
        return $this->oneCent;
    }

    /**
     * @param int|null $oneCent
     * @return $this
     */
    public function setOneCent(?int $oneCent): self
    {
        $this->oneCent = $oneCent;

        return $this;
    }

    /**
     * @return int|null
     */
    public function getTwoCents(): ?int
    {
        return $this->twoCents;
    }

    /**
     * @param int|null $twoCents
     * @return $this
     */
    public function setTwoCents(?int $twoCents): self
    {
        $this->twoCents = $twoCents;

        return $this;
    }

    /**
     * @return int|null
     */
    public function getFiveCents(): ?int
    {
        return $this->fiveCents;
    }

    /**
     * @param int|null $fiveCents
     * @return $this
     */
    public function setFiveCents(?int $fiveCents): self
    {
        $this->fiveCents = $fiveCents;

        return $this;
    }

    /**
     * @return int|null
     */
    public function getTenCents(): ?int
    {
        return $this->tenCents;
    }

    /**
     * @param int|null $tenCents
     * @return $this
     */
    public function setTenCents(?int $tenCents): self
    {
        $this->tenCents = $tenCents;

        return $this;
    }

    /**
     * @return int|null
     */
    public function getTwentyCents(): ?int
    {
        return $this->twentyCents;
    }

    /**
     * @param int|null $twentyCents
     * @return $this
     */
    public function setTwentyCents(?int $twentyCents): self
    {
        $this->twentyCents = $twentyCents;

        return $this;
    }

    /**
     * @return int|null
     */
    public function getFiftyCents(): ?int
    {
        return $this->fiftyCents;
    }

    /**
     * @param int|null $fiftyCents
     * @return $this
     */
    public function setFiftyCents(?int $fiftyCents): self
    {
        $this->fiftyCents = $fiftyCents;

        return $this;
    }

    /**
     * @return int|null
     */
    public function getOneEuro(): ?int
    {
        return $this->oneEuro;
    }

    /**
     * @param int|null $oneEuro
     * @return $this
     */
    public function setOneEuro(?int $oneEuro): self
    {
        $this->oneEuro = $oneEuro;

        return $this;
    }

    /**
     * @return int|null
     */
    public function getTwoEuros(): ?int
    {
        return $this->twoEuros;
    }

    /**
     * @param int|null $twoEuros
     * @return $this
     */
    public function setTwoEuros(?int $twoEuros): self
    {
        $this->twoEuros = $twoEuros;

        return $this;
    }

    /**
     * @return int|null
     */
    public function getFiveEuros(): ?int
    {
        return $this->fiveEuros;
    }

    /**
     * @param int|null $fiveEuros
     * @return $this
     */
    public function setFiveEuros(?int $fiveEuros): self
    {
        $this->fiveEuros = $fiveEuros;

        return $this;
    }

    /**
     * @return int|null
     */
    public function getTenEuros(): ?int
    {
        return $this->tenEuros;
    }

    /**
     * @param int|null $tenEuros
     * @return $this
     */
    public function setTenEuros(?int $tenEuros): self
    {
        $this->tenEuros = $tenEuros;

        return $this;
    }

    /**
     * @return int|null
     */
    public function getTwentyEuros(): ?int
    {
        return $this->twentyEuros;
    }

    /**
     * @param int|null $twentyEuros
     * @return $this
     */
    public function setTwentyEuros(?int $twentyEuros): self
    {
        $this->twentyEuros = $twentyEuros;

        return $this;
    }

    /**
     * @return int|null
     */
    public function getFiftyEuros(): ?int
    {
        return $this->fiftyEuros;
    }

    /**
     * @param int|null $fiftyEuros
     * @return $this
     */
    public function setFiftyEuros(?int $fiftyEuros): self
    {
        $this->fiftyEuros = $fiftyEuros;

        return $this;
    }

    /**
     * @return int|null
     */
    public function getOneHundredEuros(): ?int
    {
        return $this->oneHundredEuros;
    }

    /**
     * @param int|null $oneHundredEuros
     * @return $this
     */
    public function setOneHundredEuros(?int $oneHundredEuros): self
    {
        $this->oneHundredEuros = $oneHundredEuros;

        return $this;
    }

    /**
     * @return int|null
     */
    public function getTwoHundredEuros(): ?int
    {
        return $this->twoHundredEuros;
    }

    /**
     * @param int|null $twoHundredEuros
     * @return $this
     */
    public function setTwoHundredEuros(?int $twoHundredEuros): self
    {
        $this->twoHundredEuros = $twoHundredEuros;

        return $this;
    }

    /**
     * @return int|null
     */
    public function getFiveHundredEuros(): ?int
    {
        return $this->fiveHundredEuros;
    }

    /**
     * @param int|null $fiveHundredEuros
     * @return $this
     */
    public function setFiveHundredEuros(?int $fiveHundredEuros): self
    {
        $this->fiveHundredEuros = $fiveHundredEuros;

        return $this;
    }
}