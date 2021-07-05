<?php

namespace App\Command;

use App\Console\AbstractBookCommand;
use App\Console\Table\BooksTable;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class ReadBooksCommand extends AbstractBookCommand
{
    protected function configure()
    {
        $this->setName('commands:read:books');
        $this->setAliases(['books']);
        $this->setDescription('Read books summary');
    
        $this->addArgument(
            'names', 
            InputArgument::IS_ARRAY, 
            "Book names to be read \nYou can use SQL LIKE operator `%` in book names to select books with similar name"
        );

        $this->setCashContextOption();
        $this->setCashFormatOption();
        $this->setDateFormatOption();
        $this->setSortOption();
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $names = $input->getArgument('names');

        if (empty($names)) {
            $books = $this->bookRepository->findBy(
                ['isHidden' => false | null],
                ['name' => $this->getSortOption($input)]
            );
        } else {
            $books = $this->bookRepository->findLikeName($names, $this->getSortOption($input));
        }

        $table = new BooksTable($output);

        foreach ($books as $book) {
            $book
                ->setCashContext($this->getCashContextOption($input, $book))
                ->setCashFormat($this->getCashFormatOption($input, $book))
                ->setDateFormat($this->getDateFormatOption($input, $book))
                ;
            
            $table->addItem($book);
        }
        
        $table
            ->setColumn('Name', 'getName')
            ->render()
            ;

        return self::SUCCESS;
    }
}
