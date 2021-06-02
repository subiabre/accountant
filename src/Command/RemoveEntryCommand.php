<?php

namespace App\Command;

use App\Table\BookEntriesTable;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class RemoveEntryCommand extends AbstractBookCommand
{
    protected function configure()
    {
        $this->setName('account:remove:entry');
        $this->setAliases(['erase']);
        $this->setDescription('Delete an entry from a book');
    
        $this->addArgument('name', InputArgument::REQUIRED, 'Book name of the entry');
        $this->addArgument('id', InputArgument::REQUIRED, 'Id of the entry to be removed');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $name = $input->getArgument('name');
        $id = $input->getArgument('id');

        $book = $this->bookService->findBookByName($name);
        $entry = $this->bookService->findEntry($id);

        if (!$book || !$entry) {
            $output->writeln("The book `$name` or the entry `$entry` does not exist.");
            return self::FAILURE;
        }

        $book = $this->bookService->removeEntry($entry, $book);
        
        $this->bookService->saveBook($book);

        $table = new BookEntriesTable($output, $this->bookService);
        $table
            ->setBook($book, -1)
            ->render();

        
        return self::SUCCESS;
    }
}
