<?php

namespace App\Command;

use App\Entity\Book;
use App\Entity\Entry;
use App\Table\BookEntriesTable;
use Brick\Money\Money;
use DateTime;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class NewEntryCommand extends AbstractBookCommand
{
    protected function configure()
    {
        $this->setName('account:new:entry');
        $this->setAliases(['add']);
        $this->setDescription('Add a new entry to a book');
    
        $this->addArgument('name', InputArgument::REQUIRED, 'Book name for this entry');
        $this->addArgument('amount', InputArgument::REQUIRED, 'Amount value of this entry');
        $this->addArgument('cost', InputArgument::REQUIRED, 'Cost value of this entry');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $name = $input->getArgument('name');
        $book = $this->bookService->findBookByName($name);

        if (!$book) {
            $output->writeln(sprintf(Book::MESSAGE_MISSING, $name));
            return self::FAILURE;
        }

        $amount = floatval($input->getArgument('amount'));
        $cost = Money::of($input->getArgument('cost'), $book->getCurrency());

        $entry = new Entry();
        $entry->setDate(new DateTime());
        $entry->setAmount($amount);
        $entry->setCost($cost);

        $book = $this->bookService->addEntry($entry, $book);

        $this->bookService->saveBook($book);

        $table = new BookEntriesTable($output, $this->bookService);
        $table
            ->setBook($book, -1)
            ->render();
        
        return self::SUCCESS;
    }
}
