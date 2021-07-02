<?php

namespace App\Entity;

use App\Component\Accounting\AbstractAccounting;
use App\Component\Collection\EntryCollection;
use Brick\Money\Context;
use Brick\Money\Currency;
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
    public const DEFAULT_ACCOUNTING_KEY = 'wa';

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
     * @ORM\OneToMany(targetEntity="Entry", mappedBy="book")
     * @Serializer\Groups({"default"})
     */
    private $entries;

    /**
     * @ORM\Column(type="object")
     * @Serializer\Groups({"default"})
     */
    private $accounting;

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
        $this->entries = new EntryCollection();

        $this->entries->get;
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
     * @return EntryCollection
     */
    public function getEntries(): EntryCollection
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

    public function getAccounting(): AbstractAccounting
    {
        return $this->accounting;
    }

    public function setAccounting(AbstractAccounting $accounting): self
    {
        $this->accounting = $accounting;

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
