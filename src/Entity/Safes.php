<?php

namespace App\Entity;

use App\Repository\SafesRepository;
use App\Trait\TimeStampTrait;
use DateTimeImmutable;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Exception;
use IntlDateFormatter;
use Locale;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

#[ORM\Entity(repositoryClass: SafesRepository::class)]
#[UniqueEntity(['month', 'store'])]
#[ORM\HasLifecycleCallbacks]
class Safes
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
     * @var DateTimeImmutable|null
     */
    #[ORM\Column(type: Types::DATE_IMMUTABLE, nullable: false)]
    private ?DateTimeImmutable $month = null;

    /**
     * @var Stores|null
     */
    #[ORM\ManyToOne(inversedBy: 'safes')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Stores $store = null;

    /**
     * @var ArrayCollection|Collection
     */
    #[ORM\ManyToMany(targetEntity: ControlsCounters::class, inversedBy: 'safes')]
    #[ORM\JoinColumn(nullable: false)]
    private Collection|ArrayCollection $counters;

    /**
     * @var ControlsPeriods|null
     */
    #[ORM\ManyToOne(inversedBy: 'safes')]
    #[ORM\JoinColumn(nullable: false)]
    private ?ControlsPeriods $period = null;

    /**
     * @var ArrayCollection|Collection
     */
    #[ORM\ManyToMany(targetEntity: SafesMovementsTypes::class, inversedBy: 'safes')]
    #[ORM\JoinColumn(nullable: false)]
    private Collection|ArrayCollection $movementsTypes;

    public function __construct()
    {
        $this->counters = new ArrayCollection();
        $this->movementsTypes = new ArrayCollection();
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
            'MMMM yyyy'
        );

        return ucfirst($dateFormatter->format($this->getCreatedAt()->getTimestamp())) . ' - (' . $this->getStore() . ')';
    }

    /**
     * @param $property
     * @return mixed
     */
    public function __get($property): mixed
    {
        return $this->$property;
    }

    /**
     * @param $property
     * @return bool
     */
    public function __isset($property): bool
    {
        return isset($this->$property);
    }

    /**
     * @return int|null
     */
    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * @return string|null
     */
    public function getMonth(): ?string
    {
        return $this->month?->format('Y-m');
    }

    /**
     * @param string $month
     * @return $this
     * @throws Exception
     */
    public function setMonth(string $month): self
    {
        $this->month = new DateTimeImmutable($month);

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
     * @return Collection<int, ControlsCounters>
     */
    public function getCounters(): Collection
    {
        return $this->counters;
    }

    /**
     * @param ControlsCounters $counter
     * @return $this
     */
    public function addCounter(ControlsCounters $counter): self
    {
        if (!$this->counters->contains($counter)) {
            $this->counters->add($counter);
        }

        return $this;
    }

    /**
     * @param ControlsCounters $counter
     * @return $this
     */
    public function removeCounter(ControlsCounters $counter): self
    {
        $this->counters->removeElement($counter);

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
     * @return Collection<int, SafesMovementsTypes>
     */
    public function getMovementsTypes(): Collection
    {
        return $this->movementsTypes;
    }

    /**
     * @param SafesMovementsTypes $movementsType
     * @return $this
     */
    public function addMovementsType(SafesMovementsTypes $movementsType): self
    {
        if (!$this->movementsTypes->contains($movementsType)) {
            $this->movementsTypes->add($movementsType);
        }

        return $this;
    }

    /**
     * @param SafesMovementsTypes $movementsType
     * @return $this
     */
    public function removeMovementsType(SafesMovementsTypes $movementsType): self
    {
        $this->movementsTypes->removeElement($movementsType);

        return $this;
    }
}
