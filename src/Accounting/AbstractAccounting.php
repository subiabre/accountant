<?php

namespace App\Accounting;

use App\Entity\Book;
use App\Service\BookService;
use Brick\Math\BigDecimal;
use Brick\Math\RoundingMode;
use Brick\Money\Money;
use Symfony\Component\Serializer\Annotation as Serializer;

/**
 * @Serializer\DiscriminatorMap(typeProperty="type", mapping={
 *  "fifo"="App\Accounting\FifoAccounting",
 *  "wa"="App\Accounting\WeightedAverageAccounting"
 * })
 */
abstract class AbstractAccounting implements AccountingInterface
{
    protected Book $book;

    final public function getData(): array
    {
        return [
            'key' => self::getKey(),
            'name' => self::getName()
        ];
    }

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
        return $this->getSellValue()->minus($this->getBuyValueOfSells(), RoundingMode::UP);
    }

    public function getSellAmount(): BigDecimal
    {
        $amount = BigDecimal::of(AccountingInterface::INITIAL_AMOUNT);

        foreach ($this->book->getEntries()->getSells() as $sell) {
            $amount = $amount->plus($sell->getAmount());
        }

        return $amount;
    }

    public function getSellValue(): Money
    {
        $money = BookService::getBookMoney(0, $this->book);

        foreach ($this->book->getEntries()->getSells() as $sell) {
            $money = $money->plus($sell->getValue(), RoundingMode::UP);
        }

        return $money;
    }

    public function getBuyAmount(): BigDecimal
    {
        $amount = BigDecimal::of(AccountingInterface::INITIAL_AMOUNT);

        foreach ($this->book->getEntries()->getBuys() as $buy) {
            $amount = $amount->plus($buy->getAmount());
        }

        return $amount;
    }

    public function getBuyValue(): Money
    {
        $money = BookService::getBookMoney(0, $this->book);

        foreach ($this->book->getEntries()->getBuys() as $buy) {
            $money = $money->plus($buy->getValue(), RoundingMode::UP);
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
}
