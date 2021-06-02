<?php

namespace App\Service;

use App\Entity\Book;
use App\Entity\Entry;
use Brick\Math\RoundingMode;
use Brick\Money\Money;
use Doctrine\ORM\EntityManagerInterface;

class BookService
{
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

    public function findBook(int $bookId): ?Book
    {
        return $this->em->find(Book::class, $bookId);
    }

    public function findBookByName(string $bookName): ?Book
    {
        return $this->em->getRepository(Book::class)->findOneBy(['name' => $bookName]);
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

    public function findEntry(int $entryId): ?Entry
    {
        return $this->em->find(Entry::class, $entryId);
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

        if (count($entries) < 1) {
            return $money;
        }

        foreach ($entries as $entry) {
            $money = $money->plus($entry->getCost()->toRational(), RoundingMode::UP);
        }

        return $money;
    }

    public function calcAverageCost(Book $book): Money
    {
        $totalAmount = $this->calcTotalAmount($book);

        if ($totalAmount == (float) 0) {
            return Money::of(0, $book->getCurrency(), $book->getCashContext());
        }

        return $this->calcTotalCost($book)->dividedBy($totalAmount, RoundingMode::UP);
    }
}
