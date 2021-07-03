<?php

namespace App\Command;

use App\Console\AbstractBookCommand;
use App\Console\Table\BooksTable;
use App\Console\Table\EntriesTable;
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

            $booksTable = new BooksTable($output);
            $booksTable
                ->setColumn('Name', 'getName')
                ->addItems($books)
                ->render()
                ;

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

        $table = new EntriesTable($output);
        $table
            ->addItems($book->getEntries())
            ->render();

        return self::SUCCESS;
    }
}
