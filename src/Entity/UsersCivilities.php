<?php

namespace App\Entity;

use App\Repository\UsersCivilitiesRepository;
use App\Trait\TimeStampTrait;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Validator\Constraints as Assert;

#[UniqueEntity('label')]
#[ORM\Entity(repositoryClass: UsersCivilitiesRepository::class)]
#[ORM\HasLifecycleCallbacks]
class UsersCivilities
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
    #[ORM\OneToMany(mappedBy: 'civility', targetEntity: Users::class)]
    private Collection|ArrayCollection $users;

    /**
     * @var ArrayCollection|Collection
     */
    #[ORM\OneToMany(mappedBy: 'civility', targetEntity: Customers::class)]
    private Collection|ArrayCollection $customers;

    public function __construct()
    {
        $this->users = new ArrayCollection();
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
     * @return Collection<int, Users>
     */
    public function getUsers(): Collection
    {
        return $this->users;
    }

    /**
     * @param Users $user
     * @return $this
     */
    public function addUser(Users $user): self
    {
        if (!$this->users->contains($user)) {
            $this->users->add($user);
            $user->setCivility($this);
        }

        return $this;
    }

    /**
     * @param Users $user
     * @return $this
     */
    public function removeUser(Users $user): self
    {
        if ($this->users->removeElement($user)) {
            // set the owning side to null (unless already changed)
            if ($user->getCivility() === $this) {
                $user->setCivility(null);
            }
        }

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
            $customer->setCivility($this);
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
            if ($customer->getCivility() === $this) {
                $customer->setCivility(null);
            }
        }

        return $this;
    }
}
