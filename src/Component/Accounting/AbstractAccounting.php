<?php

namespace App\Component\Accounting;

use App\Entity\Book;
use App\Service\BookService;
use Brick\Math\BigDecimal;
use Brick\Math\RoundingMode;
use Brick\Money\Money;
use JsonSerializable;

abstract class AbstractAccounting implements AccountingInterface, JsonSerializable
{
    protected Book $book;

    final public static function getDefaultIndexName(): string
    {
        return self::getKey();
    }

    public function setBook(Book $book): AccountingInterface
    {
        $this->book = $book;

        return $this;
    }

    public function getDifferenceAmount(): BigDecimal
    {
        return $this->getBuyAmount()->minus($this->getSellAmount());
    }

    public function getDifferenceValue(): Money
    {
        return $this->getSellValue()->minus($this->getBuyValueOfSells());
    }

    public function getSellAmount(): BigDecimal
    {
        $amount = new BigDecimal(AccountingInterface::INITIAL_AMOUNT);

        foreach ($this->book->getEntries()->getSells() as $sell) {
            $amount->plus($sell->getAmount());
        }

        return $amount;
    }

    public function getSellValue(): Money
    {
        $money = BookService::getBookMoney(0, $this->book);

        foreach ($this->book->getEntries()->getSells() as $sell) {
            $money->plus($sell->getValue());
        }

        return $money;
    }

    public function getBuyAmount(): BigDecimal
    {
        $amount = new BigDecimal(AccountingInterface::INITIAL_AMOUNT);

        foreach ($this->book->getEntries()->getBuys() as $buy) {
            $amount->plus($buy->getAmount());
        }

        return $amount;
    }

    public function getBuyValue(): Money
    {
        $money = BookService::getBookMoney(0, $this->book);

        foreach ($this->book->getEntries()->getBuys() as $buy) {
            $money->plus($buy->getValue());
        }

        return $money;
    }

    public function getBuyValueAverage(): Money
    {
        $amount = $this->getBuyAmount();

        return $amount->isGreaterThan(0) 
            ? $this->getBuyValue()->dividedBy($amount, RoundingMode::UP)
            : BookService::getBookMoney(0, $this->book)
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
