<?php

namespace App\Command;

use App\Service\BookService;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class UpdateBookCommand extends BookCommand
{
    protected function configure()
    {
        $this->setName('account:update:book');
        $this->setAliases(['update']);
        $this->setDescription('Update an accounting book');

        $this->addArgument('name', InputArgument::REQUIRED, 'Book name');

        $this->setContextOption();
        $this->setHiddenOption();
        $this->setBookFormatOption();
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $name = $input->getArgument('name');
        $book = $this->bookRepository->findOneBy(['name' => $name]);

        if (!$book) {
            $output->writeln(sprintf(BookService::BOOK_MISSING, $name));
            return self::FAILURE;
        }

        $book
            ->setCashContext($this->getContextOption($input, $book))
            ->setIsHidden($this->getHiddenOption($input, $book))
            ->setFormat($this->getBookFormatOption($input, $book))
            ;

        $this->bookService->saveBook($book);

        $output->writeln(sprintf(BookService::BOOK_UPDATED, $name));

        return self::SUCCESS;
    }
}
