<?php

namespace App\Service;

use App\Entity\Book;
use App\Entity\Entry;
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

        return $book;
    }

    public function removeEntry(Entry $entry, Book $book): Book
    {
        $book->removeEntry($entry);
        $this->em->remove($entry);

        return $book;
    }

    public function deleteBook(Book $book)
    {        
        foreach ($book->getEntries() as $entry) {
            $this->removeEntry($entry, $book);
        }
    
        $this->em->remove($book);
        $this->em->flush();
    }

    public static function getBookMoney($amount = 0, Book $book): Money
    {
        return Money::of($amount, $book->getCurrency(), $book->getCashContext());
    }
}
