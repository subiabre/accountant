<?php

namespace App\Command;

use App\Entity\Book;
use Brick\Money\Currency;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class NewBookCommand extends AbstractBookCommand
{
    protected function configure()
    {
        $this->setName('account:new:book');
        $this->setAliases(['new']);
        $this->setDescription('Create a new accounting book');

        $this->addArgument('name', InputArgument::REQUIRED, 'Book name for this entry');
        $this->addArgument('currency', InputArgument::OPTIONAL, 'Default currency code for entries in this book', 'USD');
        
        $this->setHiddenOption();
        $this->setCashContextOption();
        $this->setCashFormatOption();
        $this->setDateFormatOption();
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $name = $input->getArgument('name');
        $currency = Currency::of($input->getArgument('currency'));

        $book = $this->bookService->findBookByName($name);

        if ($book) {
            $output->writeln(sprintf(Book::MESSAGE_ALREADY, $name));
            return self::FAILURE;
        }

        $book = new Book();
        $book
            ->setName($name)
            ->setCurrency($currency)
            ->setIsHidden($this->getHiddenOption($input, $book))
            ->setCashContext($this->getCashContextOption($input, $book))
            ->setCashFormat($this->getCashFormatOption($input, $book))
            ->setDateFormat($this->getDateFormatOption($input, $book))
            ;

        $this->bookService->saveNewBook($book);

        $output->writeln(sprintf(Book::MESSAGE_CREATED, $name));

        return self::SUCCESS;
    }
}
