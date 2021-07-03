<?php 

namespace App\Console\Table;

use App\Entity\Book;
use App\Entity\Entry;

class EntriesTable extends AbstractTable
{
    /** @var Entry */
    private Entry $entry;

    /** @var Book */
    private Book $book;

    /** @var Book */
    private Book $tempBook;
    
    public function configure(): void
    {
        $this->setColumn('#', 'getId');
        $this->setColumn('Date', 'getDate');
        $this->setColumn('Type', 'getType');
        $this->setColumn('Amount', 'getAmount');
        $this->setColumn('Value', 'getValue');
        $this->setColumn('Avg. Value', 'getAverage');
        $this->setColumn('T. Amount', 'getTotalAmount');
        $this->setColumn('T. Avg. Price', 'getTotalAverage');
        $this->setColumn('T. Revenue', 'getTotalRevenue');
        $this->setColumn('T. Earnings', 'getTotalEarnings');
    }

    protected function beforeRows($rows): void
    {
        $this->tempBook = new Book();
    }

    protected function onRow($row): void
    {
        $this->entry = $row;
        $this->book = $row->getBook();
        $this->tempBook
            ->addEntry($row)
            ->setAccounting($row->getBook()->getAccounting())
            ->setCurrency($row->getBook()->getCurrency())
            ->setCashContext($row->getBook()->getCashContext())
            ;
    }

    public function getId()
    {
        return $this->entry->getId();
    }

    public function getDate()
    {
        return $this->entry->getDate()->format($this->book->getDateFormat());
    }

    public function getType()
    {
        return $this->entry->getType();
    }

    public function getAmount()
    {
        return $this->entry->getAmount();
    }

    public function getValue()
    {
        return $this->entry
            ->getValue()
            ->formatTo($this->book->getCashFormat())
            ;
    }

    public function getAverage()
    {
        return $this->entry
            ->getValueAverage()
            ->formatTo($this->book->getCashFormat());
    }

    public function getTotalAmount()
    {
        return $this->tempBook
            ->getAccounting()
            ->getDifferenceAmount()
            ;
    }

    public function getTotalAverage()
    {
        return $this->tempBook
            ->getAccounting()
            ->getBuyValueAverage()
            ->formatTo($this->book->getCashFormat())
            ;
    }

    public function getTotalRevenue()
    {
        return $this->tempBook
            ->getAccounting()
            ->getSellValue()
            ->formatTo($this->book->getCashFormat())
            ;
    }

    public function getTotalEarnings()
    {
        return $this->tempBook
            ->getAccounting()
            ->getDifferenceValue()
            ->formatTo($this->book->getCashFormat())
            ;
    }
}
