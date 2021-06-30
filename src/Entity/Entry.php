<?php

namespace App\Entity;

use Brick\Money\Money;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation as Serializer;

/**
 * @ORM\Entity(repositoryClass=EntryRepository::class)
 */
class Entry
{
    public const BUY = 'buy';
    public const SELL = 'sell';

    public const MESSAGE_INVALID_TYPE = 'Valid entry types are: %s, %s is not a valid type';

    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     * @Serializer\Groups({"default"})
     */
    private $id;

    /**
     * @ORM\Column(type="string")
     * @Serializer\Groups({"default"})
     */
    private $type;

    /**
     * @ORM\Column(type="float")
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

    public function getId(): ?int
    {
        return $this->id;
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

    public function getAmount(): ?float
    {
        return $this->amount;
    }

    public function setAmount(float $amount): self
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

    public function getDate(): ?\DateTimeInterface
    {
        return $this->date;
    }

    public function setDate(\DateTimeInterface $date): self
    {
        $this->date = $date;

        return $this;
    }
}
