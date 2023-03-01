<?php

namespace App\Entity;

use App\Repository\ControlsPeriodsRepository;
use App\Trait\TimeStampTrait;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Validator\Constraints as Assert;

#[UniqueEntity('label')]
#[ORM\Entity(repositoryClass: ControlsPeriodsRepository::class)]
#[ORM\HasLifecycleCallbacks]
class ControlsPeriods
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
     * @var bool|null
     */
    #[ORM\Column(nullable: false)]
    private ?bool $debit = false;

    /**
     * @var ArrayCollection|Collection
     */
    #[ORM\OneToMany(mappedBy: 'period', targetEntity: Controls::class)]
    private Collection|ArrayCollection $controls;

    /**
     * @var ArrayCollection|Collection
     */
    #[ORM\OneToMany(mappedBy: 'period', targetEntity: Safes::class)]
    private Collection|ArrayCollection $safes;

    public function __construct()
    {
        $this->controls = new ArrayCollection();
        $this->safes = new ArrayCollection();
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
     * @return bool|null
     */
    public function isDebit(): ?bool
    {
        return $this->debit;
    }

    /**
     * @param bool $debit
     * @return $this
     */
    public function setDebit(bool $debit): self
    {
        $this->debit = $debit;

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
            $control->setPeriod($this);
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
            if ($control->getPeriod() === $this) {
                $control->setPeriod(null);
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
            $safe->setPeriod($this);
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
            // set the owning side to null (unless already changed)
            if ($safe->getPeriod() === $this) {
                $safe->setPeriod(null);
            }
        }

        return $this;
    }
}
