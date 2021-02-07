<?php

namespace App\Service;

use App\Entity\Book;
use App\Entity\Entry;
use Brick\Math\RoundingMode;
use Brick\Money\Money;
use Doctrine\ORM\EntityManagerInterface;

class BookService
{
    public const BOOK_CREATED = "The book `%s` was successfully created.";
    public const BOOK_UPDATED = "The book `%s` was successfully updated.";
    public const BOOK_MISSING = "The book `%s` does not exist.";
    public const BOOK_EXISTS = "The book `%s` already exists.";

    private $em;

    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }

    public function saveBook(Book $book)
    {
        $this->em->persist($book);
        $this->em->flush();
    }

    public function saveNewBook(Book $book)
    {
        $book->setTotalCost(Money::of(0, $book->getCurrency()));
        $book->setAverageCost(Money::of(0, $book->getCurrency()));

        $this->saveBook($book);
    }

    public function addEntry(Entry $entry, Book $book): Book
    {
        $book->addEntry($entry);
        $book->setTotalAmount($this->calcTotalAmount($book));
        $book->setTotalCost($this->calcTotalCost($book));
        $book->setAverageCost($this->calcAverageCost($book));

        return $book;
    }

    public function removeEntry(Entry $entry, Book $book): Book
    {
        $book->removeEntry($entry);
        $book->setTotalAmount($this->calcTotalAmount($book));
        $book->setTotalCost($this->calcTotalCost($book));
        $book->setAverageCost($this->calcAverageCost($book));

        return $book;
    }

    public function deleteBook(Book $book)
    {        
        $this->em->remove($book);

        foreach ($book->getEntries() as $entry) {
            $this->em->remove($entry);
        }
    
        $this->em->flush();
    }

    public function calcTotalAmount(Book $book): float
    {
        /** @var Entry[] */
        $entries = $book->getEntries();
        $total = 0;

        foreach ($entries as $entry) {
            $total += $entry->getAmount();
        }

        return $total;
    }

    public function calcTotalCost(Book $book): Money
    {
        /** @var Entry[] */
        $entries = $book->getEntries();
        $money = Money::of(0, $book->getCurrency(), $book->getCashContext());

        foreach ($entries as $entry) {
            $money = $money->plus($entry->getCost(), RoundingMode::UP);
        }

        return $money;
    }

    public function calcAverageCost(Book $book): Money
    {
        return $this->calcTotalCost($book)->dividedBy($this->calcTotalAmount($book), RoundingMode::UP);
    }
}
