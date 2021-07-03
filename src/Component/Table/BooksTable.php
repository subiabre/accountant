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

    public function getAmount()
    {
        return $this->accounting->getDifferenceAmount($this->row);
    }

    public function getAveragePrice()
    {
        return $this->accounting
            ->getBuyValueAverage($this->row)
            ->formatTo($this->row->getCashFormat())
            ;
    }

    public function getRevenue()
    {
        return $this->accounting
            ->getSellValue($this->row)
            ->formatTo($this->row->getCashFormat())
            ;
    }

    public function getEarnings()
    {
        return $this->accounting
            ->getBuyValueOfSells($this->row)
            ->formatTo($this->row->getCashFormat())
            ;
    }
}
