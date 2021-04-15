<?php

namespace App\Command;

use App\Entity\Book;
use App\Service\BookService;
use App\Table\BookEntriesTable;
use App\Table\BooksTable;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class ReadBookCommand extends BookCommand
{
    protected function configure()
    {
        $this->setName('account:read:book');
        $this->setAliases(['read']);
        $this->setDescription('Read all books overview or entries in a book');
    
        $this->addArgument('name', InputArgument::OPTIONAL, 'Book name to be read');
        $this->addArgument('max', InputArgument::OPTIONAL, 'Max number of entries to print');

        $this->setContextOption();
        $this->setSortOption();
        $this->setBookFormatOption();
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

        $book = $this->bookRepository->findOneBy(['name' => $name]);

        if (!$book) {
            $output->writeln(sprintf(BookService::BOOK_MISSING, $name));
            return self::FAILURE;
        }

        $book->setCashContext($this->getContextOption($input, $book));
        $book->setFormat($this->getBookFormatOption($input, $book));

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
