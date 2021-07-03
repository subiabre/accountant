<?php

namespace App\Component\Accounting;

use App\Component\Amount;
use App\Entity\Book;
use App\Service\BookService;
use Brick\Money\Money;

abstract class AbstractAccounting implements AccountingInterface
{
    final public static function getDefaultIndexName(): string
    {
        return self::getKey();
    }

    public function getDifferenceAmount(Book $book): Amount
    {
        return $this->getBuyAmount($book)->minus($this->getSellAmount($book));
    }

    public function getDifferenceValue(Book $book): Money
    {
        return $this->getSellValue($book)->minus($this->getBuyValueOfSells($book));
    }

    public function getSellAmount(Book $book): Amount
    {
        $amount = new Amount();

        foreach ($book->getEntries()->getSells() as $sell) {
            $amount->plus($sell->getAmount());
        }

        return $amount;
    }

    public function getSellValue(Book $book): Money
    {
        $money = BookService::getBookMoney($book);

        foreach ($book->getEntries()->getSells() as $sell) {
            $money->plus($sell->getValue());
        }

        return $money;
    }

    public function getBuyAmount(Book $book): Amount
    {
        $amount = new Amount();

        foreach ($book->getEntries()->getBuys() as $buy) {
            $amount->plus($buy->getAmount());
        }

        return $amount;
    }

    public function getBuyValue(Book $book): Money
    {
        $money = BookService::getBookMoney($book);

        foreach ($book->getEntries()->getBuys() as $sell) {
            $money->plus($sell->getValue());
        }

        return $money;
    }

    public function getBuyValueAverage(Book $book): Money
    {
        return $this->getBuyValue($book)->dividedBy($this->getBuyAmount($book));
    }
}
