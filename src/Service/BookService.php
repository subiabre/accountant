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

    public function addEntry(Entry $entry, Book $book)
    {
        $book->addEntry($entry);
        $book->setTotalAmount($this->calcTotalAmount($book));
        $book->setTotalCost($this->calcTotalCost($book));
        $book->setAverageCost($this->calcAverageCost($book));

        $this->saveBook($book);
        return $book;
    }

    public function calcTotalAmount(Book $book)
    {
        /** @var Entry[] */
        $entries = $book->getEntries();
        $total = 0;

        foreach ($entries as $entry) {
            $total += $entry->getAmount();
        }

        return $total;
    }

    public function calcTotalCost(Book $book)
    {
        /** @var Entry[] */
        $entries = $book->getEntries();
        $total = 0;

        foreach ($entries as $entry) {
            $total += $entry->getCost();
        }

        return $total;
    }

    public function calcAverageCost(Book $book)
    {
        $totalAmount = $this->calcTotalAmount($book);
        $totalCost = $this->calcTotalCost($book);

        return $totalCost / $totalAmount;
    }
}
