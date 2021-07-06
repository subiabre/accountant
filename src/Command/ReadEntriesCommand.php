<?php

namespace App\Command;

use App\Console\AbstractBookCommand;
use App\Console\Table\EntriesTable;
use App\Entity\Book;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class ReadEntriesCommand extends AbstractBookCommand
{
    protected function configure()
    {
        $this->setName('commands:read:entries');
        $this->setAliases(['read']);
        $this->setDescription('Read book entries');
    
        $this->addArgument('name', InputArgument::REQUIRED, 'Book name to be read');

        $this->setCashContextOption();
        $this->setCashFormatOption();
        $this->setDateFormatOption();
        $this->setSortOption();
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $name = $input->getArgument('name');
        $book = $this->bookService->findBookByName($name);

        if (!$book) {
            $output->writeln(sprintf(Book::MESSAGE_MISSING, $name));
            return self::FAILURE;
        }

        $book
            ->setCashContext($this->getCashContextOption($input, $book))
            ->setCashFormat($this->getCashFormatOption($input, $book))
            ->setDateFormat($this->getDateFormatOption($input, $book))
            ;

        $table = new EntriesTable($input, $output);
        $table
            ->addItems($book->getEntries())
            ->render();

        return self::SUCCESS;
    }
}
