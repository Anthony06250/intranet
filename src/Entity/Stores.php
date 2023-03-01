<?php

namespace App\Entity;

use App\Repository\StoresRepository;
use App\Trait\AddressTrait;
use App\Trait\ContactTrait;
use App\Trait\TimeStampTrait;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: StoresRepository::class)]
#[ORM\HasLifecycleCallbacks]
class Stores
{
    use AddressTrait;
    use ContactTrait;
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
    #[ORM\Column(length: 50, nullable: true)]
    #[Assert\Length(max: 50)]
    private ?string $plusCode = null;

    /**
     * @var string|null
     */
    #[ORM\Column(length: 20, nullable: true)]
    #[Assert\Length(max: 20)]
    private ?string $commercialRegisterNumber = null;

    /**
     * @var ArrayCollection|Collection
     */
    #[ORM\ManyToMany(targetEntity: Users::class, mappedBy: 'stores')]
    private Collection|ArrayCollection $users;

    /**
     * @var ArrayCollection|Collection
     */
    #[ORM\OneToMany(mappedBy: 'store', targetEntity: Controls::class)]
    private Collection|ArrayCollection $controls;

    /**
     * @var ArrayCollection|Collection
     */
    #[ORM\OneToMany(mappedBy: 'store', targetEntity: Safes::class)]
    private Collection|ArrayCollection $safes;

    /**
     * @var ArrayCollection|Collection
     */
    #[ORM\OneToMany(mappedBy: 'store', targetEntity: SafesMovements::class)]
    private Collection|ArrayCollection $safesMovements;

    /**
     * @var ArrayCollection|Collection
     */
    #[ORM\OneToMany(mappedBy: 'store', targetEntity: SafesControls::class)]
    private Collection|ArrayCollection $safesControls;

    /**
     * @var ArrayCollection|Collection
     */
    #[ORM\OneToMany(mappedBy: 'store', targetEntity: Buybacks::class)]
    private Collection|ArrayCollection $buybacks;

    /**
     * @var ArrayCollection|Collection
     */
    #[ORM\OneToMany(mappedBy: 'store', targetEntity: DepositsSales::class)]
    private Collection|ArrayCollection $depositsSales;

    /**
     * @var ArrayCollection|Collection
     */
    #[ORM\OneToMany(mappedBy: 'store', targetEntity: AdvancesPayments::class)]
    private Collection|ArrayCollection $advancesPayments;

    /**
     * @var ArrayCollection|Collection
     */
    #[ORM\OneToMany(mappedBy: 'store', targetEntity: Invoices::class)]
    private Collection|ArrayCollection $invoices;

    public function __construct()
    {
        $this->users = new ArrayCollection();
        $this->controls = new ArrayCollection();
        $this->safes = new ArrayCollection();
        $this->safesMovements = new ArrayCollection();
        $this->safesControls = new ArrayCollection();
        $this->buybacks = new ArrayCollection();
        $this->depositsSales = new ArrayCollection();
        $this->advancesPayments = new ArrayCollection();
        $this->invoices = new ArrayCollection();
    }

    /**
     * @return string
     */
    public function __toString(): string
    {
        return $this->getCity();
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
    public function getPlusCode(): ?string
    {
        return $this->plusCode;
    }

    /**
     * @param string|null $plusCode
     * @return $this
     */
    public function setPlusCode(?string $plusCode): self
    {
        $this->plusCode = $plusCode;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getCommercialRegisterNumber(): ?string
    {
        return $this->commercialRegisterNumber;
    }

    /**
     * @param string|null $commercialRegisterNumber
     * @return $this
     */
    public function setCommercialRegisterNumber(?string $commercialRegisterNumber): self
    {
        $this->commercialRegisterNumber = $commercialRegisterNumber;

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
            $user->addStore($this);
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
            $user->removeStore($this);
        }

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
            $control->setStore($this);
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
            if ($control->getStore() === $this) {
                $control->setStore(null);
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
            $safe->setStore($this);
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
            if ($safe->getStore() === $this) {
                $safe->setStore(null);
            }
        }

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
            $safesMovement->setStore($this);
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
            if ($safesMovement->getStore() === $this) {
                $safesMovement->setStore(null);
            }
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
            $safesControl->setStore($this);
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
            // set the owning side to null (unless already changed)
            if ($safesControl->getStore() === $this) {
                $safesControl->setStore(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Buybacks>
     */
    public function getBuybacks(): Collection
    {
        return $this->buybacks;
    }

    /**
     * @param Buybacks $buyback
     * @return $this
     */
    public function addBuyback(Buybacks $buyback): self
    {
        if (!$this->buybacks->contains($buyback)) {
            $this->buybacks->add($buyback);
            $buyback->setStore($this);
        }

        return $this;
    }

    /**
     * @param Buybacks $buyback
     * @return $this
     */
    public function removeBuyback(Buybacks $buyback): self
    {
        if ($this->buybacks->removeElement($buyback)) {
            // set the owning side to null (unless already changed)
            if ($buyback->getStore() === $this) {
                $buyback->setStore(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, DepositsSales>
     */
    public function getDepositsSales(): Collection
    {
        return $this->depositsSales;
    }

    /**
     * @param DepositsSales $depositsSale
     * @return $this
     */
    public function addDepositsSale(DepositsSales $depositsSale): self
    {
        if (!$this->depositsSales->contains($depositsSale)) {
            $this->depositsSales->add($depositsSale);
            $depositsSale->setStore($this);
        }

        return $this;
    }

    /**
     * @param DepositsSales $depositsSale
     * @return $this
     */
    public function removeDepositsSale(DepositsSales $depositsSale): self
    {
        if ($this->depositsSales->removeElement($depositsSale)) {
            // set the owning side to null (unless already changed)
            if ($depositsSale->getStore() === $this) {
                $depositsSale->setStore(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, AdvancesPayments>
     */
    public function getAdvancesPayments(): Collection
    {
        return $this->advancesPayments;
    }

    /**
     * @param AdvancesPayments $advancesPayment
     * @return $this
     */
    public function addAdvancesPayment(AdvancesPayments $advancesPayment): self
    {
        if (!$this->advancesPayments->contains($advancesPayment)) {
            $this->advancesPayments->add($advancesPayment);
            $advancesPayment->setStore($this);
        }

        return $this;
    }

    /**
     * @param AdvancesPayments $advancesPayment
     * @return $this
     */
    public function removeAdvancesPayment(AdvancesPayments $advancesPayment): self
    {
        if ($this->advancesPayments->removeElement($advancesPayment)) {
            // set the owning side to null (unless already changed)
            if ($advancesPayment->getStore() === $this) {
                $advancesPayment->setStore(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Invoices>
     */
    public function getInvoices(): Collection
    {
        return $this->invoices;
    }

    /**
     * @param Invoices $invoice
     * @return $this
     */
    public function addInvoice(Invoices $invoice): self
    {
        if (!$this->invoices->contains($invoice)) {
            $this->invoices->add($invoice);
            $invoice->setStore($this);
        }

        return $this;
    }

    /**
     * @param Invoices $invoice
     * @return $this
     */
    public function removeInvoice(Invoices $invoice): self
    {
        if ($this->invoices->removeElement($invoice)) {
            // set the owning side to null (unless already changed)
            if ($invoice->getStore() === $this) {
                $invoice->setStore(null);
            }
        }

        return $this;
    }
}
