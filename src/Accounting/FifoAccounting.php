<?php

namespace App\Accounting;

use App\Collection\EntryCollection;
use App\Entity\Book;
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
    private function getBuysForAmount(Book $book, BigDecimal $sellAmount): array
    {
        $allBuys = $book->getEntries()->getBuys();
        $soldBuys = new EntryCollection(new ArrayCollection([]));

        $i = 0;
        while ($sellAmount->isGreaterThan(0)) {
            $buy = $allBuys[$i];

            if ($buy->getAmount()->isGreaterThan($sellAmount)) {
                $buy->setValue($buy->getValueAverage()->multipliedBy($sellAmount, RoundingMode::UP));
                $buy->setAmount($sellAmount);
            }

            $sellAmount = $sellAmount->minus($buy->getAmount());
            $soldBuys->add($buy);

            $i++;
        }

        return $soldBuys->toArray();
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
