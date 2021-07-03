<?php

namespace App\Component\Accounting;

use App\Component\Accounting\AbstractAccounting;
use App\Component\Accounting\AccountingInterface;

class AccountingLocator
{
    private array $accountings;

    public function __construct(
        iterable $accountings
    ) {
        $this->accountings = iterator_to_array($accountings);
    }

    /**
     * @return AccountingInterface[]
     */
    public function getAll(): array
    {
        return $this->accountings;
    }

    /**
     * @param string $name
     * @return AbstractAccounting
     */
    public function getByKey(string $name): ?AbstractAccounting
    {
        foreach ($this->accountings as $accounting) {
            return $accounting::getKey() === $name ? $accounting : null;
        }
    }
}
