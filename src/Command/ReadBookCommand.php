<?php

namespace App\Command;

use App\Component\Table\BookEntriesTable;
use App\Component\Table\BooksTable;
use App\Entity\Book;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class ReadBookCommand extends AbstractBookCommand
{
    protected function configure()
    {
        $this->setName('account:read:book');
        $this->setAliases(['read']);
        $this->setDescription('Read all books overview or entries in a book');
    
        $this->addArgument('name', InputArgument::OPTIONAL, 'Book name to be read');
        $this->addArgument('max', InputArgument::OPTIONAL, 'Max number of entries to print');

        $this->setCashContextOption();
        $this->setCashFormatOption();
        $this->setDateFormatOption();
        $this->setSortOption();
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $name = $input->getArgument('name');

        if (!$name) {
            $books = $this->bookRepository->findBy(
                ['isHidden' => false | null], 
                ['name' => $this->getSortOption($input)]
            );

            $table = new BooksTable($output);
            $table
                ->setBooks($books)
                ->render();

            return self::SUCCESS;
        }

        $book = $this->bookService->findBookByName($name);

        if (!$book) {
            $output->writeln(sprintf(Book::MESSAGE_MISSING, $name));
            return self::FAILURE;
        }

        $book->setCashContext($this->getCashContextOption($input, $book));
        $book->setCashFormat($this->getCashFormatOption($input, $book));
        $book->setDateFormat($this->getDateFormatOption($input, $book));

        $table = new BookEntriesTable($output, $this->bookService);
        $table
            ->setSortOrder($this->getSortOption($input))
            ->setBook(
                $book, 
                $this->getOffset($input), 
                $this->getLength($input)
            )
            ->render();

        return self::SUCCESS;
    }

    private function getOffset($input): int
    {
        $start = (int) $input->getArgument('max');

        return $start < 0 ? $start : 0;
    }

    private function getLength($input): ?int
    {
        $start = (int) $input->getArgument('max');

        return $start < 0 ? null : $start;
    }
}
