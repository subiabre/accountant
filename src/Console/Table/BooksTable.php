<?php 

namespace App\Console\Table;

use App\Accounting\AccountingInterface;
use App\Entity\Book;
use NumberFormatter;

class BooksTable extends AbstractTable
{
    /** @var Book */
    private Book $book;

    /** @var AccountingInterface */
    private AccountingInterface $accounting;

    /** @var NumberFormatter */
    private NumberFormatter $formatter;

    public function configure(): void
    {
        $this->setColumn('Amount', 'getAmount');
        $this->setColumn('Avg. Price', 'getAveragePrice');
        $this->setColumn('Revenue', 'getRevenue');
        $this->setColumn('Earnings', 'getEarnings');
    }

    protected function beforeRows($rows): void
    {
        if (empty($rows)) return;

        $this->formatter = new NumberFormatter(
            $rows[0]->getCashFormat(),
            NumberFormatter::DEFAULT_STYLE
        );
    }

    protected function onRow($row): void
    {
        $this->book = $row;
        $this->accounting = $row->getAccounting();
    }

    public function getName()
    {
        return $this->book->getName();
    }

    public function getCurrency()
    {
        return $this->book->getCurrency()->getCurrencyCode();
    }

    public function getCashFormat()
    {
        return $this->book->getCashFormat();
    }

    public function getCashContext()
    {
        return $this->book->getCashContext();
    }

    public function getAccounting()
    {
        return $this->book->getAccounting()::getName();
    }

    public function getAccountingKey()
    {
        return $this->book->getAccounting()::getKey();
    }

    public function getAccountingName()
    {
        return $this->book->getAccounting()::getName();
    }

    public function getAmount()
    {
        return $this->formatter->format($this->accounting->getDifferenceAmount()->toFloat());
    }

    public function getAveragePrice()
    {
        return $this->accounting
            ->getBuyValueAverage()
            ->formatTo($this->book->getCashFormat())
            ;
    }

    public function getRevenue()
    {
        return $this->accounting
            ->getSellValue()
            ->formatTo($this->book->getCashFormat())
            ;
    }

    public function getEarnings()
    {
        return $this->accounting
            ->getDifferenceValue()
            ->formatTo($this->book->getCashFormat())
            ;
    }
}
