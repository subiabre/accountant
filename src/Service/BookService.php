<?php

namespace App\Service;

use App\Entity\Book;
use App\Entity\Entry;
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

        $this->saveBook($book);
        return $book;
    }

    public function readEntries(Book $book, ?int $offset = null): array
    {
        $tmpBook = new Book();

        $bookRows = [];
        foreach ($book->getEntries() as $entry) {
            $tmpBook = $this->addEntry($entry, $tmpBook);

            $bookRows[] = [
                $book->getName(),
                $entry->getId(),
                $entry->getAmount(),
                $entry->getCost(),
                $tmpBook->getTotalAmount(), 
                $tmpBook->getTotalCost(), 
                $tmpBook->getAverageCost()
            ];
        }

        if ($offset) {
            return array_slice($bookRows, $offset);
        }

        return $bookRows;
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

    public function calcTotalCost(Book $book): float
    {
        /** @var Entry[] */
        $entries = $book->getEntries();
        $total = 0;

        foreach ($entries as $entry) {
            $total += $entry->getCost();
        }

        return $total;
    }

    public function calcAverageCost(Book $book): float
    {
        $totalAmount = $this->calcTotalAmount($book);
        $totalCost = $this->calcTotalCost($book);

        return $totalCost / $totalAmount;
    }
}
