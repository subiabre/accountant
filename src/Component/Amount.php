<?php

namespace App\Component;

class Amount
{
    private float $total = 0;

    private float $available = 0;

    private float $deficit = 0;

    public function __construct(float $amount = 0)
    {
        $this->plus($amount);
    }

    /**
     * Get the total value for this amount
     * @return float
     */
    public function getTotal(): float
    {
        return $this->total;
    }

    protected function setTotal(float $total): self
    {
        $this->total = $total;

        return $this;
    }

    /**
     * Get the spendable amount value
     * @return float
     */
    public function getAvailable(): float
    {
        return $this->available;
    }

    protected function setAvailable(float $available): self
    {
        $this->available = $available;

        return $this;
    }

    /**
     * Get the debt amount value after spending
     * @return float
     */
    public function getDeficit(): float
    {
        return $this->deficit;
    }

    protected function setDeficit(float $deficit): self
    {
        $this->deficit = $deficit;

        return $this;
    }

    /**
     * Add to this amount
     * @param float $amount
     * @return self
     */
    public function plus(float $amount): self
    {
        $this->setTotal($this->total + $amount);
        $this->setAvailable($this->available + $amount);

        return $this;
    }

    /**
     * Spend from this amount
     * @param float $amount
     * @return self
     */
    public function minus(float $amount): self
    {
        $available = $this->available - $amount;
        $this->setAvailable($available < 0 ? 0 : $available);   
        $this->setDeficit($available < 0 ? $available : 0);     

        return $this;
    }
}