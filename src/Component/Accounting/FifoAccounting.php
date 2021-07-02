<?php

namespace App\Component\Accounting;

use App\Component\Amount;
use App\Component\Collection\EntryCollection;
use App\Entity\Book;
use Brick\Money\Money;

class FifoAccounting extends AbstractAccounting
{
    public static function getKey(): string
    {
        return 'fifo';
    }

    public static function getDescription(): string
    {
        return 'First In, First Out';
    }

    private function getBuysForAmount(Book $book, Amount $sellAmount): EntryCollection
    {
        $allBuys = $book->getEntries()->getBuys();
        $soldBuys = new EntryCollection();

        $i = 0;
        while ($sellAmount->getAvailable() > 0) {
            $buy = $allBuys[$i];

            if ($buy->getAmount()->getAvailable() > $sellAmount->getAvailable()) {
                $buy->setValue($buy->getValueAverage()->multipliedBy($sellAmount->getAvailable()));
                $buy->setAmount($sellAmount);
            }

            $sellAmount->minus($buy->getAmount());
            $soldBuys->add($buy);

            $i++;
        }

        return $soldBuys;
    }

    public function getBuyValueOfSells(Book $book): Money
    {
        $soldBuys = $this->getBuysForAmount($book, $this->getSellAmount($book));

        $cost = Money::of(0, $book->getCurrency(), $book->getCashContext());
        foreach ($soldBuys->getSells() as $soldBuy) {
            $cost->plus($soldBuy->getValue());
        }

        return $cost;
    }
}
