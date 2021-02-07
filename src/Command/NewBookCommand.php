<?php

namespace App\Command;

use App\Entity\Book;
use App\Service\BookService;
use Brick\Money\Currency;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class NewBookCommand extends BookCommand
{
    protected function configure()
    {
        $this->setName('account:new:book');
        $this->setAliases(['new']);
        $this->setDescription('Create a new accounting book');

        $this->addArgument('name', InputArgument::REQUIRED, 'Book name for this entry');
        $this->addArgument('currency', InputArgument::OPTIONAL, 'Default currency code for entries in this book', 'USD');
        
        $this->setContextOption();
        $this->setHiddenOption();
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $name = $input->getArgument('name');
        $currency = Currency::of($input->getArgument('currency'));

        $book = $this->bookRepository->findOneBy(['name' => $name]);

        if ($book) {
            $output->writeln(sprintf(BookService::BOOK_EXISTS, $name));
            return self::FAILURE;
        }

        $book = new Book();
        $book
            ->setName($name)
            ->setCurrency($currency)
            ->setCashContext($this->getContextOption($input, $book))
            ->setIsHidden($this->getHiddenOption($input, $book))
            ;

        $this->bookService->saveNewBook($book);

        $output->writeln(sprintf(BookService::BOOK_CREATED, $name));

        return self::SUCCESS;
    }
}
