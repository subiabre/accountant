<?php

namespace App\Accounting;

use App\Entity\Book;
use App\Service\BookService;
use Brick\Math\BigDecimal;
use Brick\Math\RoundingMode;
use Brick\Money\Money;

abstract class AbstractAccounting implements AccountingInterface
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

    protected function getEntriesAmount(array $entries): BigDecimal
    {
        $amount = BigDecimal::of(AccountingInterface::INITIAL_AMOUNT);

        foreach ($entries as $entry) {
            $amount = $amount->plus($entry->getAmount());
        }

        return $amount;
    }

    protected function getEntriesValue(array $entries): Money
    {
        $money = BookService::getBookMoney(0, $this->book);

        foreach ($entries as $entry) {
            $price = $entry->getValue()->toRational();
            $money = $money->plus($price, RoundingMode::UP);
        }

        return $money;
    }

    public function getDifferenceAmount(): BigDecimal
    {
        return $this->getBuyAmount()->minus($this->getSellAmount());
    }

    public function getDifferenceValue(): Money
    {
        $cost = $this->getBuyValueOfSells()->toRational();
        return $this->getSellValue()->minus($cost, RoundingMode::UP);
    }

    public function getSellAmount(): BigDecimal
    {
        return $this->getEntriesAmount($this->book->getEntries()->getSells());
    }

    public function getSellValue(): Money
    {
        return $this->getEntriesValue($this->book->getEntries()->getSells());
    }

    public function getBuyAmount(): BigDecimal
    {
        return $this->getEntriesAmount($this->book->getEntries()->getBuys());
    }

    public function getBuyValue(): Money
    {
        return $this->getEntriesValue($this->book->getEntries()->getBuys());
    }

    public function getBuyValueAverage(): Money
    {
        $amount = $this->getBuyAmount();

        return $amount->isGreaterThan(0) 
            ? $this->getBuyValue()->dividedBy($amount, RoundingMode::UP)
            : BookService::getBookMoney(0, $this->book)
            ;
    }
}
