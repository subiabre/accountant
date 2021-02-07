<?php

namespace App\Entity;

use Brick\Money\Context;
use Brick\Money\Currency;
use Brick\Money\Money;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\MappedSuperclass
 * @ORM\Entity(repositoryClass=BookRepository::class)
 */
class Book
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $name;

    /**
     * @ORM\Column(type="object", length=255)
     */
    private $currency;

    /**
     * @ORM\ManyToMany(targetEntity="Entry", cascade={"persist"})
     * @ORM\JoinTable(name="book_entries",
     *      joinColumns={@ORM\JoinColumn(name="book_id", referencedColumnName="id")},
     *      inverseJoinColumns={@ORM\JoinColumn(name="entry_id", referencedColumnName="id", unique=true)}
     *      )
     */
    private $entries;

    /**
     * @ORM\Column(type="float", nullable=true)
     */
    private $totalAmount;

    /**
     * @ORM\Column(type="object", nullable=true)
     */
    private $totalCost;

    /**
     * @ORM\Column(type="object", nullable=true)
     */
    private $averageCost;

    /**
     * @ORM\Column(type="object", nullable=true)
     */
    private $cashContext;

    public function __construct()
    {
        $this->entries = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    public function getCurrency(): ?Currency
    {
        return $this->currency;
    }

    public function setCurrency(Currency $currency): self
    {
        $this->currency = $currency;

        return $this;
    } 

    /**
     * @return Collection|Entry[]
     */
    public function getEntries(): Collection
    {
        return $this->entries;
    }

    public function addEntry(Entry $entry): self
    {
        if (!$this->entries->contains($entry)) {
            $this->entries[] = $entry;
        }

        return $this;
    }

    public function removeEntry(Entry $entry): self
    {
        $this->entries->removeElement($entry);

        return $this;
    }

    public function getTotalAmount(): ?float
    {
        return $this->totalAmount;
    }

    public function setTotalAmount(?float $totalAmount): self
    {
        $this->totalAmount = $totalAmount;

        return $this;
    }

    public function getTotalCost(): ?Money
    {
        return $this->totalCost;
    }

    public function setTotalCost(?Money $totalCost): self
    {
        $this->totalCost = $totalCost;

        return $this;
    }

    public function getAverageCost(): ?Money
    {
        return $this->averageCost;
    }

    public function setAverageCost(?Money $averageCost): self
    {
        $this->averageCost = $averageCost;

        return $this;
    }

    public function getCashContext(): Context
    {
        return $this->cashContext;
    }

    public function setCashContext(Context $cashContext): self
    {
        $this->cashContext = $cashContext;

        return $this;
    }
}
