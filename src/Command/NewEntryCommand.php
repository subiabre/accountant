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

class NewBuyCommand extends AbstractBookCommand
{
    protected function configure()
    {
        $this->setName('account:new:add');
        $this->setAliases(['add']);
        $this->setDescription('Add a new buy entry to a book');
    
        $this->addArgument('type', InputArgument::REQUIRED, 'Type of entry, either `buy` or `sell`');
        $this->addArgument('name', InputArgument::REQUIRED, 'Book name for this entry');
        $this->addArgument('amount', InputArgument::REQUIRED, 'Amount value of this entry');
        $this->addArgument('cost', InputArgument::REQUIRED, 'Cost value of this entry');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $type = $input->getArgument('type');

        if (!$this->entryService->isValidEntryType($type)) {
            $output->writeln(sprintf(Entry::MESSAGE_INVALID_TYPE, $type));
            return self::FAILURE;
        }

        $name = $input->getArgument('name');
        $book = $this->bookService->findBookByName($name);

        if (!$book) {
            $output->writeln(sprintf(Book::MESSAGE_MISSING, $name));
            return self::FAILURE;
        }

        $amount = floatval($input->getArgument('amount'));
        $cost = Money::of($input->getArgument('cost'), $book->getCurrency());

        $entry = new Entry();
        $entry
            ->setType($type)
            ->setDate(new DateTime())
            ->setAmount($amount)
            ->setValue($cost)
            ;

        $book = $this->bookService->addEntry($entry, $book);

        $this->bookService->saveBook($book);

        $table = new BookEntriesTable($output, $this->bookService);
        $table
            ->setBook($book, -1)
            ->render();
        
        return self::SUCCESS;
    }
}
