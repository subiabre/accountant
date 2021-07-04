<?php

namespace App\Entity;

use App\Service\BookService;
use Brick\Math\BigDecimal;
use Brick\Math\RoundingMode;
use Brick\Money\Money;
use DateTime;
use DateTimeZone;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation as Serializer;

/**
 * @ORM\Entity(repositoryClass=EntryRepository::class)
 */
class Entry
{
    public const MESSAGE_INVALID_TYPE = 'Valid entry types are `buy` or `sell`, %s is not a valid type';

    public const BUY = 'buy';
    public const SELL = 'sell';

    public const DEFAULT_TYPE = 'buy';

    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     * @Serializer\Groups({"default"})
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity="Book", inversedBy="entries")
     * @Serializer\Groups({"default"})
     */
    private $book;

    /**
     * @ORM\Column(type="string")
     * @Serializer\Groups({"default"})
     */
    private $type;

    /**
     * @ORM\Column(type="object")
     * @Serializer\Groups({"default"})
     */
    private $amount;

    /**
     * @ORM\Column(type="object")
     * @Serializer\Groups({"default"})
     */
    private $value;

    /**
     * @ORM\Column(type="date")
     * @Serializer\Groups({"default"})
     */
    private $date;

    public function __construct()
    {
        $this->date = new DateTime('now', new DateTimeZone('UTC'));
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getBook(): Book
    {
        return $this->book;
    }

    public function setBook(Book $book): self
    {
        $this->book = $book;
        
        return $this;
    }

    public function getType(): string
    {
        return $this->type;
    }

    public function setType(string $type): self
    {
        $this->type = $type;

        return $this;
    }

    public function getAmount(): BigDecimal
    {
        return $this->amount;
    }

    public function setAmount(BigDecimal $amount): self
    {
        $this->amount = $amount;

        return $this;
    }

    public function getValue(): ?Money
    {
        return $this->value;
    }

    public function setValue(Money $value): self
    {
        $this->value = $value;

        return $this;
    }

    public function getValueAverage(): Money
    {
        return $this->amount->isGreaterThan(0)
            ? $this->value->dividedBy($this->amount, RoundingMode::UP) 
            : BookService::getBookMoney(0, $this->book)
            ;
    }

    public function getDate(): ?\DateTime
    {
        $this->date->setTimezone(new DateTimeZone(date_default_timezone_get()));

        return $this->date;
    }

    public function setDate(\DateTime $date): self
    {
        $this->date = $date;

        return $this;
    }
}
