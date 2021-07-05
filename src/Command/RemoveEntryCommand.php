<?php

namespace App\Command;

use App\Console\AbstractBookCommand;
use App\Console\Table\EntriesTable;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class RemoveEntryCommand extends AbstractBookCommand
{
    protected function configure()
    {
        $this->setName('commands:remove:entry');
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
        $entry = $this->entryService->findEntry($id);

        if (!$book || !$entry) {
            $output->writeln("The book `$name` or the entry `$entry` does not exist.");
            return self::FAILURE;
        }

        $book = $this->bookService->removeEntry($entry, $book);
        
        $this->bookService->saveBook($book);

        $table = new EntriesTable($output);
        $table
            ->addItems($book->getEntries())
            ->render()
            ;

        
        return self::SUCCESS;
    }
}
