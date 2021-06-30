<?php

namespace App\Entity;

use Brick\Money\Context;
use Brick\Money\Currency;
use Brick\Money\Money;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation as Serializer;

/**
 * @ORM\MappedSuperclass
 * @ORM\Entity(repositoryClass=App\Repository\BookRepository::class)
 */
class Book
{
    public const MESSAGE_CREATED = "The book `%s` was successfully created.";
    public const MESSAGE_UPDATED = "The book `%s` was successfully updated.";
    public const MESSAGE_MISSING = "The book `%s` does not exist.";
    public const MESSAGE_ALREADY = "The book `%s` already exists.";
    public const MESSAGE_DELETED = "The book `%s` was successfully deleted.";
    public const MESSAGE_ENTRIES = "The book `%s` contains %s entries.";

    public const SORT_ASCENDING = 'ASC';
    public const SORT_DESCENDING = 'DESC';

    public const DEFAULT_HIDDEN = false;
    public const DEFAULT_CASH_CONTEXT = 2;
    public const DEFAULT_CASH_FORMAT = 'en_US';
    public const DEFAULT_CASH_CURRENCY = 'USD';
    public const DEFAULT_DATE_FORMAT = 'Y-m-d';

    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     * @Serializer\Groups({"default"})
     */
    private $name;

    /**
     * @ORM\Column(type="object", length=255)
     * @Serializer\Groups({"default"})
     */
    private $currency;

    /**
     * @ORM\ManyToMany(targetEntity="Entry", cascade={"persist"})
     * @ORM\JoinTable(name="book_entries",
     *      joinColumns={@ORM\JoinColumn(name="book_id", referencedColumnName="id")},
     *      inverseJoinColumns={@ORM\JoinColumn(name="entry_id", referencedColumnName="id", unique=true)}
     *      )
     * @Serializer\Groups({"default"})
     */
    private $entries;

    /**
     * @ORM\Column(type="float", nullable=true)
     * @Serializer\Groups({"default"})
     */
    private $totalAmount;

    /**
     * @ORM\Column(type="float", nullable=true)
     * @Serializer\Groups({"default"})
     */
    private $currentAmount;

    /**
     * @ORM\Column(type="object", nullable=true)
     * @Serializer\Groups({"default"})
     */
    private $totalCost;

    /**
     * @ORM\Column(type="object", nullable=true)
     * @Serializer\Groups({"default"})
     */
    private $averageCost;

    /**
     * @ORM\Column(type="object", nullable=true)
     * @Serializer\Groups({"default"})
     */
    private $totalProfit;

    /**
     * @ORM\Column(type="object", nullable=true)
     * @Serializer\Groups({"default"})
     */
    private $totalDifference;

    /**
     * @ORM\Column(type="boolean", nullable=true)
     */
    private $isHidden;

    /**
     * @ORM\Column(type="object", nullable=true)
     */
    private $cashContext;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $cashFormat;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $dateFormat;

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

    public function getCurrentAmount(): ?float
    {
        return $this->currentAmount;
    }

    public function setCurrentAmount(?float $currentAmount): self
    {
        $this->currentAmount = $currentAmount;

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

    public function getTotalProfit(): ?Money
    {
        return $this->totalProfit;
    }

    public function setTotalProfit(?Money $totalProfit): self
    {
        $this->totalProfit = $totalProfit;

        return $this;
    }

    public function getTotalDifference(): ?Money
    {
        return $this->totalDifference;
    }

    public function setTotalDifference(?Money $totalDifference): self
    {
        $this->totalDifference = $totalDifference;

        return $this;
    }

    public function isHidden(): bool
    {
        return (bool) $this->isHidden;
    }

    public function setIsHidden(bool $isHidden): self
    {
        $this->isHidden = $isHidden;

        return $this;
    }

    public function getCashContext(): ?Context
    {
        return $this->cashContext;
    }

    public function setCashContext(?Context $cashContext): self
    {
        $this->cashContext = $cashContext;

        return $this;
    }

    public function getCashFormat(): ?string
    {
        return $this->cashFormat;
    }

    public function setCashFormat(string $cashFormat): self
    {
        $this->cashFormat = $cashFormat;

        return $this;
    }

    public function getDateFormat(): ?string
    {
        return $this->dateFormat;
    }

    public function setDateFormat(string $dateFormat): self
    {
        $this->dateFormat = $dateFormat;

        return $this;
    }
}
