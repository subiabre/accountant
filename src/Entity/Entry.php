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
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     * @Serializer\Groups({"default"})
     */
    private $id;

    /**
     * @ORM\Column(type="float")
     * @Serializer\Groups({"default"})
     */
    private $amount;

    /**
     * @ORM\Column(type="object")
     * @Serializer\Groups({"default"})
     */
    private $cost;

    /**
     * @ORM\Column(type="date")
     * @Serializer\Groups({"default"})
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
