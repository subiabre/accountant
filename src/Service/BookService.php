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
        $this->saveBook($this->setBookData($book));
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

        return $this->setBookData($book);
    }

    public function removeEntry(Entry $entry, Book $book): Book
    {
        $book->removeEntry($entry);

        return $this->setBookData($book);
    }

    public function deleteBook(Book $book)
    {        
        $this->em->remove($book);

        foreach ($book->getEntries() as $entry) {
            $this->em->remove($entry);
        }
    
        $this->em->flush();
    }

    public function setBookData(Book $book): Book
    {
        return $book
            ->setTotalAmount($this->calcTotalAmount($book))
            ->setTotalCost($this->calcTotalCost($book))
            ->setAverageCost($this->calcAverageCost($book))
            ->setTotalProfit($this->calcTotalProfit($book))
            ->setTotalDifference($this->calcTotalDifference($book))
            ;
    }

    public function getBookEntriesByType(Book $book, string $type): array
    {
        return array_values(array_filter(
            (array) $book->getEntries(), 
            function ($entry) use ($type) {
                if ($entry->getType() == $type) return $entry;
            }
        ));
    }

    public function getBookMoney(Book $book): Money
    {
        return Money::of(0, $book->getCurrency(), $book->getCashContext());
    }

    public function calcTotalAmount(Book $book): float
    {
        /** @var Entry[] */
        $entries = $book->getEntries();
        $total = 0;

        foreach ($entries as $entry) {
            switch ($entry->getType()) {
                case Entry::SELL:
                    $total -= $entry->getAmount();
                    break;
                
                case Entry::BUY:
                    $total += $entry->getAmount();
                    break;
            }
        }

        return $total;
    }

    public function calcTotalCost(Book $book): Money
    {
        $entries = $this->getBookEntriesByType($book, Entry::BUY);
        $money = $this->getBookMoney($book);

        if (count($entries) < 1) {
            return $money;
        }

        foreach ($entries as $entry) {
            $money = $money->plus($entry->getValue()->toRational(), RoundingMode::UP);
        }

        return $money;
    }

    public function calcAverageCost(Book $book): Money
    {
        $totalAmount = $this->calcTotalAmount($book);

        if ($totalAmount == (float) 0) {
            return $this->getBookMoney($book);
        }

        return $this->calcTotalCost($book)->dividedBy($totalAmount, RoundingMode::UP);
    }

    public function calcTotalProfit(Book $book): Money
    {
        $entries = $this->getBookEntriesByType($book, Entry::SELL);
        $money = $this->getBookMoney($book);

        if (count($entries) < 1) {
            return $money;
        }

        foreach ($entries as $entry) {
            $money = $money->plus($entry->getValue()->toRational(), RoundingMode::UP);
        }

        return $money;
    }

    public function calcTotalDifference(Book $book): Money
    {
        $totalAmount = $this->calcTotalAmount($book);

        if ($totalAmount == (float) 0) {
            return $this->getBookMoney($book);
        }
        
        return $this->calcTotalCost($book)->minus($this->calcTotalProfit($book), RoundingMode::UP);
    }
}
