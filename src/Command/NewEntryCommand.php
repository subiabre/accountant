<?php

namespace App\Command;

use App\Console\AbstractBookCommand;
use App\Console\Table\BooksTable;
use App\Entity\Book;
use App\Entity\Entry;
use App\Service\BookService;
use Brick\Math\BigDecimal;
use DateTime;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class NewEntryCommand extends AbstractBookCommand
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

        $amount = BigDecimal::of($input->getArgument('amount'));
        $cost = BookService::getBookMoney($input->getArgument('cost'), $book);

        $entry = new Entry();
        $entry
            ->setBook($book)
            ->setType($type)
            ->setDate(new DateTime())
            ->setAmount($amount)
            ->setValue($cost)
            ;

        $book = $this->bookService->addEntry($entry, $book);

        $this->bookService->saveBook($book);

        $table = new BooksTable($output);
        $table
            ->addItem($book)
            ->render();
        
        return self::SUCCESS;
    }
}
