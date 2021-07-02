<?php

namespace App\Service;

use App\Component\Accounting\AbstractAccounting;
use App\Component\Accounting\AccountingInterface;

class AccountingService
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
    public function getAccountings(): array
    {
        return $this->accountings;
    }

    /**
     * @param string $name
     * @return AbstractAccounting
     */
    public function getAccountingByKey(string $name): ?AbstractAccounting
    {
        return $this->accountings[$name];
    }
}
