<?php

namespace App\Component\Accounting;

use App\Component\Amount;
use App\Entity\Book;
use App\Service\BookService;
use Brick\Math\RoundingMode;
use Brick\Money\Money;
use JsonSerializable;

abstract class AbstractAccounting implements AccountingInterface, JsonSerializable
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
        $money = BookService::getBookMoney(0, $book);

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
        $money = BookService::getBookMoney(0, $book);

        foreach ($book->getEntries()->getBuys() as $buy) {
            $money->plus($buy->getValue());
        }

        return $money;
    }

    public function getBuyValueAverage(Book $book): Money
    {
        $amount = $this->getBuyAmount($book)->getAvailable();

        return $amount > 0 
            ? $this->getBuyValue($book)->dividedBy($amount, RoundingMode::UP)
            : BookService::getBookMoney(0, $book)
            ;
    }

    public function jsonSerialize()
    {
        return [
            'key' => self::getKey(),
            'name' => self::getName() 
        ];
    }
}
