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

    public function rowSetup($row): void
    {
        $this->accounting = $row->getAccounting();
    }

    public function getName()
    {
        $this->row->getName();
    }

    public function getCurrency()
    {
        $this->row->getCurrency()->getCurrencyCode();
    }

    public function getCashFormat()
    {
        $this->row->getCashFormat();
    }

    public function getCashContext()
    {
        $this->row->getCashContext();
    }

    public function getAmount()
    {
        $this->accounting->getDifferenceAmount($this->row);
    }

    public function getAveragePrice()
    {
        $this->accounting->getBuyValueAverage($this->row);
    }

    public function getRevenue()
    {
        $this->accounting->getSellValue($this->row);
    }

    public function getEarnings()
    {
        $this->accounting->getBuyValueOfSells($this->row);
    }
}
