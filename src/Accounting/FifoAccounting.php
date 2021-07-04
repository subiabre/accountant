<?php

namespace App\Accounting;

use App\Collection\EntryCollection;
use App\Entity\Book;
use App\Entity\Entry;
use App\Service\BookService;
use Brick\Math\BigDecimal;
use Brick\Math\RoundingMode;
use Brick\Money\Money;
use Doctrine\Common\Collections\ArrayCollection;

class FifoAccounting extends AbstractAccounting
{
    public static function getKey(): string
    {
        return 'fifo';
    }

    public static function getName(): string
    {
        return 'First In, First Out';
    }

    /**
     * @return Entry[]
     */
    protected function getBuysForAmount(Book $book, BigDecimal $amount): array
    {
        $buys = new EntryCollection(new ArrayCollection([]));

        $i = 0;
        while ($amount->isGreaterThan(0)) {
            $buy = $book->getEntries()->getBuys()[$i];

            if ($buy->getAmount()->isGreaterThanOrEqualTo($amount)) {
                $endBuy = new Entry();
                $endBuy
                    ->setType(Entry::BUY)
                    ->setValue($buy->getValueAverage()->multipliedBy($amount, RoundingMode::UP))
                    ->setAmount($amount)
                    ;

                $amount = BigDecimal::of(0);
                $buys->add($endBuy);
                continue;
            }

            $amount = $amount->minus($buy->getAmount());
            $buys->add($buy);

            $i++;
        }

        return $buys->toArray();
    }

    public function getBuyValueOfSells(): Money
    {
        $soldBuys = $this->getBuysForAmount($this->book, $this->getSellAmount());

        $cost = BookService::getBookMoney(0, $this->book);
        foreach ($soldBuys as $soldBuy) {
            $cost = $cost->plus($soldBuy->getValue(), RoundingMode::UP);
        }

        return $cost;
    }
}
