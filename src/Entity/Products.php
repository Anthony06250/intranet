<?php

namespace App\Entity;

use App\Repository\ProductsRepository;
use App\Trait\TimeStampTrait;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: ProductsRepository::class)]
#[ORM\HasLifecycleCallbacks]
class Products
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
    #[ORM\Column(length: 50, nullable: true)]
    #[Assert\NotBlank]
    #[Assert\Length(min:2, max: 50)]
    private ?string $label = null;

    /**
     * @var string|null
     */
    #[ORM\Column(length: 20, nullable: true)]
    #[Assert\Length(max: 20)]
    private ?string $serialNumber = null;

    /**
     * @var string|null
     */
    #[ORM\Column(length: 13, nullable: true)]
    #[Assert\Length(max: 13)]
    private ?string $barCode = null;

    /**
     * @var float|null
     */
    #[ORM\Column(nullable: true)]
    private ?float $price = null;

    /**
     * @var ArrayCollection|Collection
     */
    #[ORM\ManyToMany(targetEntity: Invoices::class, mappedBy: 'products')]
    private Collection|ArrayCollection $invoices;

    /**
     * @var ArrayCollection|Collection
     */
    #[ORM\ManyToMany(targetEntity: AdvancesPayments::class, mappedBy: 'products')]
    private Collection|ArrayCollection $advancesPayments;

    /**
     * @var ArrayCollection|Collection
     */
    #[ORM\ManyToMany(targetEntity: DepositsSales::class, mappedBy: 'products')]
    private Collection|ArrayCollection $depositsSales;

    /**
     * @var ArrayCollection|Collection
     */
    #[ORM\ManyToMany(targetEntity: Buybacks::class, mappedBy: 'products')]
    private Collection|ArrayCollection $buybacks;

    public function __construct()
    {
        $this->invoices = new ArrayCollection();
        $this->advancesPayments = new ArrayCollection();
        $this->depositsSales = new ArrayCollection();
        $this->buybacks = new ArrayCollection();
    }

    /**
     * @return string
     */
    public function __toString(): string
    {
        return $this->getLabel() ?? '';
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
     * @param string|null $label
     * @return $this
     */
    public function setLabel(?string $label): self
    {
        $this->label = strtolower($label);

        return $this;
    }

    /**
     * @return string|null
     */
    public function getSerialNumber(): ?string
    {
        return $this->serialNumber;
    }

    /**
     * @param string|null $serialNumber
     * @return $this
     */
    public function setSerialNumber(?string $serialNumber): self
    {
        $this->serialNumber = $serialNumber;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getBarCode(): ?string
    {
        return $this->barCode;
    }

    /**
     * @param string|null $barCode
     * @return $this
     */
    public function setBarCode(?string $barCode): self
    {
        $this->barCode = $barCode;

        return $this;
    }

    /**
     * @return float|null
     */
    public function getPrice(): ?float
    {
        return $this->price;
    }

    /**
     * @param float|null $price
     * @return $this
     */
    public function setPrice(?float $price): self
    {
        $this->price = $price;

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
            $invoice->addProduct($this);
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
            $invoice->removeProduct($this);
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
            $advancesPayment->addProduct($this);
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
            $advancesPayment->removeProduct($this);
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
            $depositsSale->addProduct($this);
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
            $depositsSale->removeProduct($this);
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
            $buyback->addProduct($this);
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
            $buyback->removeProduct($this);
        }

        return $this;
    }
}
