<?php

namespace App\Component\Accounting;

use App\Component\Amount;
use App\Component\Collection\EntryCollection;
use App\Entity\Book;
use Brick\Money\Money;

class FifoAccounting extends AbstractAccounting
{
    public static function getDefaultIndexName(): string
    {
        return 'fifo';
    }

    private function getSellsForAmount(Book $book, Amount $amount): EntryCollection
    {
        $allBuys = $book->getEntries()->getBuys();
        $soldBuys = new EntryCollection();

        $i = 0;
        while ($amount->getAvailable() > 0) {
            $buy = $allBuys[$i];

            if ($buy->getAmount()->getAvailable() > $amount->getAvailable()) {
                $buy->setValue(
                        $this->getEntryAverageValue($buy)->multipliedBy($amount->getAvailable())
                );
                $buy->setAmount(($amount));
            }

            $amount->minus($buy->getAmount()->getAvailable());
            $soldBuys->add($buy);

            $i++;
        }

        return $soldBuys;
    }

    public function getSellCost(Book $book): Money
    {
        $soldBuys = $this->getSellsForAmount($book, $this->getSellAmount($book));

        $cost = Money::of(0, $book->getCurrency(), $book->getCashContext());
        foreach ($soldBuys->getSells() as $soldBuy) {
            $cost->plus($soldBuy->getValue());
        }

        return $cost;
    }
}
