<?php

namespace App\Service;

use App\Component\Accounting\AbstractAccounting;

class AccountingService
{
    private array $accountings;

    public function __construct(
        iterable $accountings
    ) {
        $this->accountings = iterator_to_array($accountings);
    }

    public function getAccountings(): array
    {
        return $this->accountings;
    }

    public function getAccountingByName(string $name): ?AbstractAccounting
    {
        return $this->accountings[$name];
    }
}
