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
    private Collection|ArrayCollection $controlsCounters;

    /**
     * @var ControlsPeriods|null
     */
    #[ORM\ManyToOne(inversedBy: 'safes')]
    #[ORM\JoinColumn(nullable: false)]
    private ?ControlsPeriods $controlsPeriod = null;

    /**
     * @var ArrayCollection|Collection
     */
    #[ORM\ManyToMany(targetEntity: SafesMovementsTypes::class, inversedBy: 'safes')]
    #[ORM\JoinColumn(nullable: false)]
    private Collection|ArrayCollection $safesMovementsTypes;

    public function __construct()
    {
        $this->controlsCounters = new ArrayCollection();
        $this->safesMovementsTypes = new ArrayCollection();
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
    public function getControlsCounters(): Collection
    {
        return $this->controlsCounters;
    }

    /**
     * @param ControlsCounters $controlsCounter
     * @return $this
     */
    public function addControlsCounters(ControlsCounters $controlsCounter): self
    {
        if (!$this->controlsCounters->contains($controlsCounter)) {
            $this->controlsCounters->add($controlsCounter);
        }

        return $this;
    }

    /**
     * @param ControlsCounters $controlsCounter
     * @return $this
     */
    public function removeControlsCounters(ControlsCounters $controlsCounter): self
    {
        $this->controlsCounters->removeElement($controlsCounter);

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
     * @return Collection<int, SafesMovementsTypes>
     */
    public function getSafesMovementsTypes(): Collection
    {
        return $this->safesMovementsTypes;
    }

    /**
     * @param SafesMovementsTypes $safesMovementsType
     * @return $this
     */
    public function addSafesMovementsTypes(SafesMovementsTypes $safesMovementsType): self
    {
        if (!$this->safesMovementsTypes->contains($safesMovementsType)) {
            $this->safesMovementsTypes->add($safesMovementsType);
        }

        return $this;
    }

    /**
     * @param SafesMovementsTypes $safesMovementsType
     * @return $this
     */
    public function removeSafesMovementsTypes(SafesMovementsTypes $safesMovementsType): self
    {
        $this->safesMovementsTypes->removeElement($safesMovementsType);

        return $this;
    }
}
