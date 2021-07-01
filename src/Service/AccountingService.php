<?php

namespace App\Service;

use App\Component\Accounting\AbstractAccounting;

class AccountingService
{
    private $accountingMethods;

    public function __construct(
        iterable $accountingMethods
    ) {
        $this->accountingMethods = $accountingMethods;
    }

    public function getAccountings(): iterable
    {
        return $this->accountingMethods;
    }

    public function getAccountingByName(string $accountingClassName): ?AbstractAccounting
    {
        var_dump($this->accountingMethods);
    }
}
