<?php

namespace App\Component\Accounting;

use App\Entity\Book;
use Brick\Money\Money;

class WeightedAverageAccounting extends AbstractAccounting
{
    public function getSellCost(Book $book): Money
    {
        $averageCost = $this->getBuyValue($book)->dividedBy($this->getBuyAmount($book));

        return $averageCost->multipliedBy($this->getSellAmount($book));
    }
}
