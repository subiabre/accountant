<?php 

namespace App\Component\Table;

use App\Component\Accounting\AccountingInterface;

class BooksTable extends AbstractTable
{
    /** @var AccountingInterface */
    private $accounting;

    public function configure(): void
    {
        $this->setColumn('Amount', 'getAmount');
        $this->setColumn('Avg. Price', 'getAveragePrice');
        $this->setColumn('Revenue', 'getRevenue');
        $this->setColumn('Earnings', 'getEarnings');
    }

    protected function beforeRows($rows): void
    {
        return;
    }

    protected function onRow($row): void
    {
        $this->accounting = $row->getAccounting();
    }

    public function getName()
    {
        return $this->row->getName();
    }

    public function getCurrency()
    {
        return $this->row->getCurrency()->getCurrencyCode();
    }

    public function getCashFormat()
    {
        return $this->row->getCashFormat();
    }

    public function getCashContext()
    {
        return $this->row->getCashContext();
    }

    public function getAccounting()
    {
        return $this->row->getAccounting()::getName();
    }

    public function getAccountingKey()
    {
        return $this->row->getAccounting()::getKey();
    }

    public function getAmount()
    {
        return $this->accounting->getDifferenceAmount();
    }

    public function getAveragePrice()
    {
        return $this->accounting
            ->getBuyValueAverage()
            ->formatTo($this->row->getCashFormat())
            ;
    }

    public function getRevenue()
    {
        return $this->accounting
            ->getSellValue()
            ->formatTo($this->row->getCashFormat())
            ;
    }

    public function getEarnings()
    {
        return $this->accounting
            ->getBuyValueOfSells()
            ->formatTo($this->row->getCashFormat())
            ;
    }
}
