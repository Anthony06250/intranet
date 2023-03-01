<?php

namespace App\Entity;

use App\Repository\SafesControlsRepository;
use App\Trait\CurrenciesTrait;
use App\Trait\TimeStampTrait;
use DateTimeImmutable;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use IntlDateFormatter;
use Locale;

#[ORM\Entity(repositoryClass: SafesControlsRepository::class)]
#[ORM\HasLifecycleCallbacks]
class SafesControls
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
    #[ORM\ManyToOne(inversedBy: 'safesControls')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Users $user = null;

    /**
     * @var Stores|null
     */
    #[ORM\ManyToOne(inversedBy: 'safesControls')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Stores $store = null;

    /**
     * @var ArrayCollection|Collection
     */
    #[ORM\ManyToMany(targetEntity: ControlsCounters::class, inversedBy: 'safesControls')]
    private Collection|ArrayCollection $counters;

    /**
     * @var float|null
     */
    #[ORM\Column(nullable: false)]
    private ?float $result = null;

    /**
     * @var float|null
     */
    #[ORM\Column(nullable: false)]
    private ?float $total = null;

    public function __construct()
    {
        $this->counters = new ArrayCollection();
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

        return $this->getStore() . ' - ' . $dateFormatter->format($this->getCreatedAt()->getTimestamp());
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
     * @return Collection<int, ControlsCounters>
     */
    public function getCounters(): Collection
    {
        return $this->counters;
    }

    /**
     * @param ControlsCounters $controlsCounter
     * @return $this
     */
    public function addCounter(ControlsCounters $controlsCounter): self
    {
        if (!$this->counters->contains($controlsCounter)) {
            $this->counters->add($controlsCounter);
        }

        return $this;
    }

    /**
     * @param ControlsCounters $controlsCounter
     * @return $this
     */
    public function removeCounter(ControlsCounters $controlsCounter): self
    {
        $this->counters->removeElement($controlsCounter);

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
    public function getTotal(): ?float
    {
        return $this->total;
    }

    /**
     * @param float $total
     * @return $this
     */
    public function setTotal(float $total): self
    {
        $this->total = $total;

        return $this;
    }
}
