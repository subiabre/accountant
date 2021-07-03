<?php

namespace App\Component\Accounting;

use App\Component\Amount;
use App\Entity\Book;
use Brick\Money\Money;

interface AccountingInterface
{
    public static function getKey(): string;

    public static function getName(): string;

    /**
     * Get the difference between buys amount and sells amount \
     * Buys - Sells
     */
    public function getDifferenceAmount(Book $book): Amount;

    /**
     * Get the difference between buys value and sells value \
     * Sells - Sells' Buy Value
     */
    public function getDifferenceValue(Book $book): Money;

    /**
     * Get the total amount of sells
     */
    public function getSellAmount(Book $book): Amount;

    /**
     * Get the total value of sells
     */
    public function getSellValue(Book $book): Money;

    /**
     * Get the total amount of buys
     */
    public function getBuyAmount(Book $book): Amount;

    /**
     * Get the total value of buys
     */
    public function getBuyValue(Book $book): Money;

    /**
     * Get the average value of buys
     */
    public function getBuyValueAverage(Book $book): Money;

    /**
     * Get the total value for sold buys
     */
    public function getBuyValueOfSells(Book $book): Money;
}
