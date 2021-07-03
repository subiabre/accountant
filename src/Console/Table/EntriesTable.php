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
    }

    protected function beforeRows($rows): void
    {
        $this->tempBook = new Book();
    }

    protected function onRow($row): void
    {
        $this->entry = $row;
        $this->book = $row->getBook();
        $this->tempBook->addEntry($row);
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
}
