<?php

namespace App\Entity;

use App\Repository\SafesMovementsTypesRepository;
use App\Trait\TimeStampTrait;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Validator\Constraints as Assert;

#[UniqueEntity('label')]
#[ORM\Entity(repositoryClass: SafesMovementsTypesRepository::class)]
#[ORM\HasLifecycleCallbacks]
class SafesMovementsTypes
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
     * @var ArrayCollection|Collection
     */
    #[ORM\OneToMany(mappedBy: 'safesMovementsType', targetEntity: SafesMovements::class)]
    private Collection|ArrayCollection $safesMovements;

    /**
     * @var ArrayCollection|Collection
     */
    #[ORM\ManyToMany(targetEntity: Safes::class, mappedBy: 'safesMovementsTypes')]
    private Collection|ArrayCollection $safes;

    public function __construct()
    {
        $this->safesMovements = new ArrayCollection();
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
     * @return Collection<int, SafesMovements>
     */
    public function getSafesMovements(): Collection
    {
        return $this->safesMovements;
    }

    /**
     * @param SafesMovements $safesMovement
     * @return $this
     */
    public function addSafesMovement(SafesMovements $safesMovement): self
    {
        if (!$this->safesMovements->contains($safesMovement)) {
            $this->safesMovements->add($safesMovement);
            $safesMovement->setSafesMovementsType($this);
        }

        return $this;
    }

    /**
     * @param SafesMovements $safesMovement
     * @return $this
     */
    public function removeSafesMovement(SafesMovements $safesMovement): self
    {
        if ($this->safesMovements->removeElement($safesMovement)) {
            // set the owning side to null (unless already changed)
            if ($safesMovement->getSafesMovementsType() === $this) {
                $safesMovement->setSafesMovementsType(null);
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
            $safe->addSafesMovementsType($this);
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
            $safe->removeSafesMovementsType($this);
        }

        return $this;
    }
}
