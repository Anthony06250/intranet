<?php

namespace App\Entity;

use App\Repository\CustomersTypesIdsRepository;
use App\Trait\TimeStampTrait;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Validator\Constraints as Assert;

#[UniqueEntity('label')]
#[ORM\Entity(repositoryClass: CustomersTypesIdsRepository::class)]
#[ORM\HasLifecycleCallbacks]
class CustomersTypesIds
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
    #[ORM\OneToMany(mappedBy: 'customersTypesId', targetEntity: Customers::class)]
    private Collection|ArrayCollection $customers;

    public function __construct()
    {
        $this->customers = new ArrayCollection();
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
        return $this->label;
    }

    /**
     * @param string $label
     * @return $this
     */
    public function setLabel(string $label): self
    {
        $this->label = $label;

        return $this;
    }

    /**
     * @return Collection<int, Customers>
     */
    public function getCustomers(): Collection
    {
        return $this->customers;
    }

    /**
     * @param Customers $customer
     * @return $this
     */
    public function addCustomer(Customers $customer): self
    {
        if (!$this->customers->contains($customer)) {
            $this->customers->add($customer);
            $customer->setCustomersTypesId($this);
        }

        return $this;
    }

    /**
     * @param Customers $customer
     * @return $this
     */
    public function removeCustomer(Customers $customer): self
    {
        if ($this->customers->removeElement($customer)) {
            // set the owning side to null (unless already changed)
            if ($customer->getCustomersTypesId() === $this) {
                $customer->setCustomersTypesId(null);
            }
        }

        return $this;
    }
}
