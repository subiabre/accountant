<?php 

namespace App\Table;

use App\Entity\Book;
use App\Entity\Entry;
use App\Service\BookService;
use Symfony\Component\Console\Helper\Table;
use Symfony\Component\Console\Output\OutputInterface;

class BookEntriesTable extends Table
{
    private $bookService;

    private $sortOrder;

    private $entries;

    public function __construct(OutputInterface $outputInterface, BookService $bookService)
    {
        parent::__construct($outputInterface);

        $this->bookService = $bookService;
        $this->sortOrder = Book::SORT_ASCENDING;
    }

    public function setSortOrder(string $sort): self
    {
        $this->sortOrder = $sort;

        return $this;
    }

    public function setBook(Book $book, ?int $offset = 0, ?int $length = null): self
    {
        $tableBook = new Book();
        $entries = [];

        $cashFormat = $book->getCashFormat();
        $dateFormat = $book->getDateFormat();

        $tableBook->setCurrency($book->getCurrency());
        $tableBook->setCashContext($book->getCashContext());
        $tableBook->setCashFormat($book->getCashFormat());
        
        $this->setHeaders([
            'Book',
            '#',
            'Date',
            'Type',
            'Amount',
            'Value',
            'Book Amount',
            'Book Cost t.',
            'Book Cost avg.',
            'Book Profit',
            'Book Difference'
        ]);

        /** @var Entry */
        foreach ($book->getEntries() as $entry) {
            $tableBook = $this->bookService->addEntry($entry, $tableBook);

            $entries[] = [
                $book->getName(),
                $entry->getId(),
                $entry->getDate()->format($dateFormat),
                $entry->getType(),
                $entry->getAmount(),
                $entry->getValue()->formatTo($cashFormat),
                $tableBook->getCurrentAmount(),
                $tableBook->getTotalCost()->formatTo($cashFormat),
                $tableBook->getAverageCost()->formatTo($cashFormat),
                $tableBook->getTotalProfit()->formatTo($cashFormat),
                $tableBook->getTotalDifference()->formatTo($cashFormat)
            ];
        }

        if ($this->sortOrder == Book::SORT_DESCENDING) {
            $entries = array_reverse($entries);
        }

        if ($offset !== 0 || $length) {
            $entries = array_slice($entries, $offset, $length);
        }

        $this->entries = $entries;

        return $this;
    }

    public function render()
    {
        $entries = $this->entries;

        $this->addRows($entries);
        
        parent::render();
    }
}
