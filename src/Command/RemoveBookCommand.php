<?php

namespace App\Command;

use App\Console\AbstractBookCommand;
use App\Entity\Book;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\ConfirmationQuestion;

class RemoveBookCommand extends AbstractBookCommand
{
    protected function configure()
    {
        $this->setName('account:remove:book');
        $this->setAliases(['drop']);
        $this->setDescription('Delete a book and all the entries it contains');
    
        $this->addArgument('name', InputArgument::REQUIRED, 'Book name');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $name = $input->getArgument('name');

        $book = $this->bookService->findBookByName($name);

        if (!$book) {
            $output->writeln(sprintf(Book::MESSAGE_MISSING, $name));
            return self::FAILURE;
        }

        $output->writeln(sprintf(Book::MESSAGE_ENTRIES, $name, count($book->getEntries())));

        $helper = $this->getHelper('question');
        $question = new ConfirmationQuestion("Do you want to delete this book? (y/n): ", false);

        if (!$helper->ask($input, $output, $question)) {
            return self::SUCCESS;
        }

        $this->bookService->deleteBook($book);

        $output->writeln(sprintf(Book::MESSAGE_DELETED, $name));
        
        return self::SUCCESS;
    }
}
