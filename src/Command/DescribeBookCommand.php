<?php

namespace App\Command;

use App\Entity\Book;
use App\Table\BooksTable;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class DescribeBookCommand extends AbstractBookCommand
{
    protected function configure()
    {
        $this->setName('account:describe:book');
        $this->setAliases(['desc']);
        $this->setDescription('Show data from a book');
    
        $this->addArgument('name', InputArgument::REQUIRED, 'Book name to be read');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $name = $input->getArgument('name');

        $book = $this->bookService->findBookByName($name);

        if (!$book) {
            $output->writeln(sprintf(Book::MESSAGE_MISSING, $name));
            return self::FAILURE;
        }

        $table = new BooksTable($output);
        $table
            ->setExtraColumns(
                ['Currency', 'Cash Format'],
                [$book->getCurrency()->getCurrencyCode(), $book->getCashFormat()]
            )
            ->setBooks([$book])
            ->render();

        return self::SUCCESS;
    }
}