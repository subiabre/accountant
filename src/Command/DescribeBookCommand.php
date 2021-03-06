<?php

namespace App\Command;

use App\Console\AbstractBookCommand;
use App\Console\Table\BooksTable;
use App\Entity\Book;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

class DescribeBookCommand extends AbstractBookCommand
{
    protected function configure()
    {
        $this->setName('commands:describe:book');
        $this->setAliases(['desc']);
        $this->setDescription('Show a book details');
    
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

        $table = new BooksTable($input, $output);
        $table
            ->setColumn('Currency', 'getCurrency')
            ->setColumn('Format', 'getCashFormat')
            ->setColumn('Accounting', 'getAccounting')
            ->addItem($book)
            ->render()
            ;

        return self::SUCCESS;
    }
}
