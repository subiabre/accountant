<?php

namespace App\Command;

use App\Entity\Book;
use Brick\Money\Currency;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class UpdateBookCommand extends AbstractBookCommand
{
    protected function configure()
    {
        $this->setName('account:update:book');
        $this->setAliases(['update']);
        $this->setDescription('Update an accounting book');

        $this->addArgument('name', InputArgument::REQUIRED, 'Book name');
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

        if (!$book) {
            $output->writeln(sprintf(Book::MESSAGE_MISSING, $name));
            return self::FAILURE;
        }

        $book
            ->setCurrency($currency)
            ->setIsHidden($this->getHiddenOption($input, $book))
            ->setCashContext($this->getCashContextOption($input, $book))
            ->setCashFormat($this->getCashFormatOption($input, $book))
            ->setDateFormat($this->getDateFormatOption($input, $book))
            ;

        $this->bookService->saveBook($book);

        $output->writeln(sprintf(Book::MESSAGE_UPDATED, $name));

        return self::SUCCESS;
    }
}
