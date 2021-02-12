<?php

namespace App\Entity;

use Brick\Money\Money;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=EntryRepository::class)
 */
class Entry
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     * @Groups({"default"})
     */
    private $id;

    /**
     * @ORM\Column(type="float")
     * @Groups({"default"})
     */
    private $amount;

    /**
     * @ORM\Column(type="object")
     * @Groups({"default"})
     */
    private $cost;

    /**
     * @ORM\Column(type="date")
     * @Groups({"default"})
     */
    private $date;

    public function getId(): ?int
    {
        return $this->id;
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

    public function getCost(): ?Money
    {
        return $this->cost;
    }

    public function setCost(Money $cost): self
    {
        $this->cost = $cost;

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
