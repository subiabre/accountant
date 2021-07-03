<?php

namespace App\Component\Accounting;

use App\Entity\Book;
use Brick\Math\BigDecimal;
use Brick\Money\Money;

interface AccountingInterface
{
    public const INITIAL_AMOUNT = '0.0';

    public static function getKey(): string;

    public static function getName(): string;

    public function setBook(Book $book): AccountingInterface;

    /**
     * Get the difference between buys amount and sells amount \
     * Buys - Sells
     */
    public function getDifferenceAmount(): BigDecimal;

    /**
     * Get the difference between buys value and sells value \
     * Sells - Sells' Buy Value
     */
    public function getDifferenceValue(): Money;

    /**
     * Get the total amount of sells
     */
    public function getSellAmount(): BigDecimal;

    /**
     * Get the total value of sells
     */
    public function getSellValue(): Money;

    /**
     * Get the total amount of buys
     */
    public function getBuyAmount(): BigDecimal;

    /**
     * Get the total value of buys
     */
    public function getBuyValue(): Money;

    /**
     * Get the average value of buys
     */
    public function getBuyValueAverage(): Money;

    /**
     * Get the total value for sold buys
     */
    public function getBuyValueOfSells(): Money;
}
