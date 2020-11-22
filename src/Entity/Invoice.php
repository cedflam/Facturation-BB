<?php

namespace App\Entity;

use App\Repository\InvoiceRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;


/**
 * @ORM\Entity(repositoryClass=InvoiceRepository::class)
 *
 */
class Invoice
{
    const FACTURE_A_REGLER = false;
    const FACTURE_REGLEE = true;
    const FACTURE_ATTENTE = 'attente';
    const FACTURE_ACOMPTE = 'acompte';
    const FACTURE_FINALE = 'finale';

    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="float")
     */
    private $totalHt;

    /**
     * @ORM\Column(type="float")
     */
    private $totalTtc;

    /**
     * @ORM\Column(type="float", nullable=true)
     */
    private $totalAdvance;

    /**
     * @ORM\Column(type="boolean")
     */
    private $state;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $typeInvoice;

    /**
     * @ORM\Column(type="datetime")
     */
    private $createdAt;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $reference;

    /**
     * @ORM\Column(type="float")
     */
    private $remainingCapital;

    /**
     * @ORM\ManyToOne(targetEntity=Customer::class, inversedBy="invoices")
     */
    private $customer;

    /**
     * @ORM\OneToOne(targetEntity=Estimate::class, inversedBy="invoice", cascade={"persist", "remove"})
     * @ORM\JoinColumn(nullable=false)
     */
    private $estimate;

    /**
     * @ORM\OneToMany(targetEntity=Description::class, mappedBy="invoice")
     */
    private $descriptions;

    /**
     * @ORM\OneToMany(targetEntity=Advance::class, mappedBy="invoice", cascade={"remove"})
     */
    private $advances;

    public function __construct()
    {
        $this->descriptions = new ArrayCollection();
        $this->advances = new ArrayCollection();
    }


    public function getId()
    {
        return $this->id;
    }

    public function getTotalHt(): ?float
    {
        return $this->totalHt;
    }

    public function setTotalHt(float $totalHt): self
    {
        $this->totalHt = $totalHt;

        return $this;
    }

    public function getTotalTtc(): ?float
    {
        return $this->totalTtc;
    }

    public function setTotalTtc(float $totalTtc): self
    {
        $this->totalTtc = $totalTtc;

        return $this;
    }

    public function getTotalAdvance(): ?float
    {
        return $this->totalAdvance;
    }

    public function setTotalAdvance(float $totalAdvance): self
    {
        $this->totalAdvance = $totalAdvance;

        return $this;
    }

    public function getState(): ?bool
    {
        return $this->state;
    }

    public function setState(bool $state): self
    {
        $this->state = $state;

        return $this;
    }

    public function getTypeInvoice(): ?string
    {
        return $this->typeInvoice;
    }

    public function setTypeInvoice(string $typeInvoice): self
    {
        $this->typeInvoice = $typeInvoice;

        return $this;
    }

    public function getCreatedAt(): ?\DateTimeInterface
    {
        return $this->createdAt;
    }

    public function setCreatedAt(\DateTimeInterface $createdAt): self
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    public function getReference(): ?string
    {
        return $this->reference;
    }

    public function setReference(string $reference): self
    {
        $this->reference = $reference;

        return $this;
    }

    public function getRemainingCapital(): ?float
    {
        return $this->remainingCapital;
    }

    public function setRemainingCapital(float $remainingCapital): self
    {
        $this->remainingCapital = $remainingCapital;

        return $this;
    }

    public function getCustomer(): ?Customer
    {
        return $this->customer;
    }

    public function setCustomer(?Customer $customer): self
    {
        $this->customer = $customer;

        return $this;
    }

    public function getEstimate(): ?Estimate
    {
        return $this->estimate;
    }

    public function setEstimate(Estimate $estimate): self
    {
        $this->estimate = $estimate;

        return $this;
    }

    /**
     * @return Collection|Description[]
     */
    public function getDescriptions(): Collection
    {
        return $this->descriptions;
    }

    public function addDescription(Description $description): self
    {
        if (!$this->descriptions->contains($description)) {
            $this->descriptions[] = $description;
            $description->setInvoice($this);
        }

        return $this;
    }

    public function removeDescription(Description $description): self
    {
        if ($this->descriptions->removeElement($description)) {
            // set the owning side to null (unless already changed)
            if ($description->getInvoice() === $this) {
                $description->setInvoice(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|Advance[]
     */
    public function getAdvances(): Collection
    {
        return $this->advances;
    }

    public function addAdvance(Advance $advance): self
    {
        if (!$this->advances->contains($advance)) {
            $this->advances[] = $advance;
            $advance->setInvoice($this);
        }

        return $this;
    }

    public function removeAdvance(Advance $advance): self
    {
        if ($this->advances->removeElement($advance)) {
            // set the owning side to null (unless already changed)
            if ($advance->getInvoice() === $this) {
                $advance->setInvoice(null);
            }
        }

        return $this;
    }



}
