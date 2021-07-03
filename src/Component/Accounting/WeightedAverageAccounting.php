<?php

namespace App\Component\Accounting;

use App\Entity\Book;
use Brick\Money\Money;

class WeightedAverageAccounting extends AbstractAccounting
{
    public static function getKey(): string
    {
        return 'wa';
    }

    public static function getName(): string
    {
        return 'Weighted Average';
    }

    public function getBuyValueOfSells(): Money
    {
        return $this->getBuyValueAverage()->multipliedBy($this->getSellAmount());
    }
}
