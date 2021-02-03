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
            'Average Cost'
        ]);
    }

    public function setBooks(array $books): self
    {
        /** @var Book */
        foreach ($books as $book) {
            $this->addRow([
                $book->getName(),
                $book->getTotalAmount(),
                $book->getTotalCost()->formatTo('en_US'),
                $book->getAverageCost()->formatTo('en_US')
            ]);
        }

        return $this;
    }
}
