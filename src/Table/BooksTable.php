<?php 

namespace App\Table;

use App\Entity\Book;
use Symfony\Component\Console\Helper\Table;
use Symfony\Component\Console\Output\OutputInterface;

class BooksTable extends Table
{

    public function __construct(OutputInterface $outputInterface)
    {
        parent::__construct($outputInterface);

        $this->setHeaders([
            'Book',
            'Total Amount',
            'Total Cost',
            'Average Cost',
            'Total Profit',
            'Difference',
            'Currency',
            'Cash Format',
        ]);
    }

    public function setBooks(array $books): self
    {
        /** @var Book */
        foreach ($books as $book) {
            $cashFormat = $book->getCashFormat();

            $this->addRow([
                $book->getName(),
                $book->getTotalAmount(),
                $book->getTotalCost()->formatTo($cashFormat),
                $book->getAverageCost()->formatTo($cashFormat),
                $book->getTotalProfit()->formatTo($cashFormat),
                $book->getCurrency()->getCurrencyCode(),
                $cashFormat
            ]);
        }

        return $this;
    }
}
