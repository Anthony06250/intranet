<?php

namespace App\Entity;

use App\Repository\AdvancesPaymentsMethodsRepository;
use App\Trait\TimeStampTrait;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Validator\Constraints as Assert;

#[UniqueEntity('label')]
#[ORM\Entity(repositoryClass: AdvancesPaymentsMethodsRepository::class)]
#[ORM\HasLifecycleCallbacks]
class AdvancesPaymentsMethods
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
    #[ORM\OneToMany(mappedBy: 'paymentsMethod', targetEntity: AdvancesPayments::class)]
    private Collection|ArrayCollection $advancesPayments;

    /**
     * @var ArrayCollection|Collection
     */
    #[ORM\OneToMany(mappedBy: 'paymentsMethod', targetEntity: Invoices::class)]
    private Collection|ArrayCollection $invoices;

    public function __construct()
    {
        $this->advancesPayments = new ArrayCollection();
        $this->invoices = new ArrayCollection();
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
            $advancesPayment->setPaymentsMethod($this);
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
            if ($advancesPayment->getPaymentsMethod() === $this) {
                $advancesPayment->setPaymentsMethod(null);
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
            $invoice->setPaymentsMethod($this);
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
            if ($invoice->getPaymentsMethod() === $this) {
                $invoice->setPaymentsMethod(null);
            }
        }

        return $this;
    }
}
