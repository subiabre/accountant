<?php 

namespace App\Table;

use App\Entity\Book;
use Symfony\Component\Console\Helper\Table;
use Symfony\Component\Console\Output\OutputInterface;

class BooksTable extends Table
{
    private array $headers = [
        'Book',
        'Total Amount',
        'Total Cost',
        'Average Cost',
        'Total Profit',
        'total Difference'
    ];

    private $extraRows = [];

    public function __construct(OutputInterface $outputInterface)
    {
        parent::__construct($outputInterface);

        $this->setHeaders($this->headers);
    }

    public function setExtraColumns(array $headers, array $rows)
    {
        $this->setHeaders(array_merge($this->headers, $headers));
        $this->extraRows = $rows;

        return $this;
    }

    public function setBooks(array $books): self
    {
        /** @var Book */
        foreach ($books as $book) {
            $cashFormat = $book->getCashFormat();

            $this->addRow(array_merge([
                $book->getName(),
                $book->getTotalAmount(),
                $book->getTotalCost()->formatTo($cashFormat),
                $book->getAverageCost()->formatTo($cashFormat),
                $book->getTotalProfit()->formatTo($cashFormat),
                $book->getTotalDifference()->formatTo($cashFormat)
            ], $this->extraRows));
        }

        return $this;
    }
}
