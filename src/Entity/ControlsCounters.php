<?php

namespace App\Entity;

use App\Repository\ControlsCountersRepository;
use App\Trait\TimeStampTrait;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Validator\Constraints as Assert;

#[UniqueEntity('label')]
#[ORM\Entity(repositoryClass: ControlsCountersRepository::class)]
#[ORM\HasLifecycleCallbacks]
class ControlsCounters
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
     * @var string|null
     */
    #[ORM\Column(length: 50, unique: true, nullable: false)]
    #[Assert\NotBlank]
    #[Assert\Length(min:2, max: 50)]
    private ?string $label = null;

    /**
     * @var float|null
     */
    #[ORM\Column(nullable: true)]
    private ?float $cashFund = null;

    /**
     * @var bool|null
     */
    #[ORM\Column(nullable: false)]
    private ?bool $reverse = false;

    /**
     * @var ArrayCollection|Collection
     */
    #[ORM\OneToMany(mappedBy: 'counter', targetEntity: Controls::class)]
    private Collection|ArrayCollection $controls;

    /**
     * @var ArrayCollection|Collection
     */
    #[ORM\ManyToMany(targetEntity: Safes::class, mappedBy: 'counters')]
    private Collection|ArrayCollection $safes;

    /**
     * @var ArrayCollection|Collection
     */
    #[ORM\ManyToMany(targetEntity: SafesControls::class, mappedBy: 'counters')]
    private Collection|ArrayCollection $safesControls;

    public function __construct()
    {
        $this->controls = new ArrayCollection();
        $this->safes = new ArrayCollection();
        $this->safesControls = new ArrayCollection();
    }

    /**
     * @return string
     */
    public function __toString(): string
    {
        return $this->getLabel();
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
    public function getLabel(): ?string
    {
        return $this->label ? ucfirst($this->label) : null;
    }

    /**
     * @param string $label
     * @return $this
     */
    public function setLabel(string $label): self
    {
        $this->label = strtolower($label);

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
     * @param float|null $cashFund
     * @return $this
     */
    public function setCashFund(?float $cashFund): self
    {
        $this->cashFund = $cashFund;

        return $this;
    }

    /**
     * @return bool|null
     */
    public function isReverse(): ?bool
    {
        return $this->reverse;
    }

    /**
     * @param bool $reverse
     * @return $this
     */
    public function setReverse(bool $reverse): self
    {
        $this->reverse = $reverse;

        return $this;
    }

    /**
     * @return Collection<int, Controls>
     */
    public function getControls(): Collection
    {
        return $this->controls;
    }

    /**
     * @param Controls $control
     * @return $this
     */
    public function addControl(Controls $control): self
    {
        if (!$this->controls->contains($control)) {
            $this->controls->add($control);
            $control->setCounter($this);
        }

        return $this;
    }

    /**
     * @param Controls $control
     * @return $this
     */
    public function removeControl(Controls $control): self
    {
        if ($this->controls->removeElement($control)) {
            // set the owning side to null (unless already changed)
            if ($control->getCounter() === $this) {
                $control->setCounter(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Safes>
     */
    public function getSafes(): Collection
    {
        return $this->safes;
    }

    /**
     * @param Safes $safe
     * @return $this
     */
    public function addSafe(Safes $safe): self
    {
        if (!$this->safes->contains($safe)) {
            $this->safes->add($safe);
            $safe->addCounter($this);
        }

        return $this;
    }

    /**
     * @param Safes $safe
     * @return $this
     */
    public function removeSafe(Safes $safe): self
    {
        if ($this->safes->removeElement($safe)) {
            $safe->removeCounter($this);
        }

        return $this;
    }

    /**
     * @return Collection<int, SafesControls>
     */
    public function getSafesControls(): Collection
    {
        return $this->safesControls;
    }

    /**
     * @param SafesControls $safesControl
     * @return $this
     */
    public function addSafesControl(SafesControls $safesControl): self
    {
        if (!$this->safesControls->contains($safesControl)) {
            $this->safesControls->add($safesControl);
            $safesControl->addCounter($this);
        }

        return $this;
    }

    /**
     * @param SafesControls $safesControl
     * @return $this
     */
    public function removeSafesControl(SafesControls $safesControl): self
    {
        if ($this->safesControls->removeElement($safesControl)) {
            $safesControl->removeCounter($this);
        }

        return $this;
    }
}
