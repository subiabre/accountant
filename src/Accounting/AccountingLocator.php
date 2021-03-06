<?php

namespace App\Accounting;

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
            if ($accounting::getKey() === $name) return $accounting;
        }

        return null;
    }
}
