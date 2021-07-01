<?php

namespace App\Component\Accounting;

use App\Component\Amount;
use App\Entity\Book;
use App\Entity\Entry;
use App\Service\BookService;
use App\Service\EntryService;
use Brick\Money\Money;

abstract class AbstractAccounting implements AccountingInterface
{
    private $bookService;
    private $entryService;

    public function __construct(
        BookService $bookService,
        EntryService $entryService
    )
    {
        $this->bookService = $bookService;
        $this->entryService = $entryService;
    }

    public function getSellAmount(Book $book): Amount
    {
        $amount = new Amount();

        foreach ($book->getEntries()->getSells() as $sell) {
            $amount->plus($sell->getAmount()->getTotal());
        }

        return $amount;
    }

    public function getSellValue(Book $book): Money
    {
        $money = $this->bookService->getBookMoney($book);

        foreach ($book->getEntries()->getSells() as $sell) {
            $money->plus($sell->getValue());
        }

        return $money;
    }

    public function getBuyAmount(Book $book): Amount
    {
        $amount = new Amount();

        foreach ($book->getEntries()->getBuys() as $buy) {
            $amount->plus($buy->getAmount()->getTotal());
        }

        return $amount;
    }

    public function getBuyValue(Book $book): Money
    {
        $money = $this->bookService->getBookMoney($book);

        foreach ($book->getEntries()->getBuys() as $sell) {
            $money->plus($sell->getValue());
        }

        return $money;
    }

    public function getEntryAverageValue(Entry $entry): Money
    {
        return $entry->getValue()->dividedBy($entry->getAmount()->getTotal());
    }
}
