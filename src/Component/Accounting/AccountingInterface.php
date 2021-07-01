<?php

namespace App\Component\Accounting;

use App\Component\Amount;
use App\Entity\Book;
use Brick\Money\Money;

interface AccountingInterface
{
    public static function getDefaultIndexName(): string;

    public function getSellAmount(Book $book): Amount;

    public function getSellValue(Book $book): Money;

    public function getSellCost(Book $book): Money;

    public function getBuyAmount(Book $book): Amount;

    public function getBuyValue(Book $book): Money;
}
