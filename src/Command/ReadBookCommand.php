<?php

namespace App\Command;

use App\Repository\BookRepository;
use App\Service\BookService;
use App\Table\BookEntriesTable;
use App\Table\BooksTable;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class ReadBookCommand extends Command
{
    /** @var BookRepository */
    private $bookRepository;

    /** @var BookService */
    private $bookService;

    public function __construct(BookRepository $bookRepository, BookService $bookService)
    {
        parent::__construct();

        $this->bookRepository = $bookRepository;
        $this->bookService = $bookService;
    }

    protected function configure()
    {
        $this->setName('account:read:book');
        $this->setAliases(['read']);
        $this->setDescription('Read all books overview or entries in a book');
    
        $this->addArgument('name', InputArgument::OPTIONAL, 'Book name to be read');
        $this->addArgument('max', InputArgument::OPTIONAL, 'Max number of entries to print');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $name = $input->getArgument('name');
        $start = (int) $input->getArgument('max');

        if (!$name) {
            $books = $this->bookRepository->findAll();

            $table = new BooksTable($output);
            $table
                ->setBooks($books)
                ->render();

            return self::SUCCESS;
        }

        $book = $this->bookRepository->findOneBy(['name' => $name]);

        if (!$book) {
            $output->writeln("The book `$name` does not exist.");
            return self::FAILURE;
        }

        if ($start < 0) {
            $offset = $start;
            $length = null;
        } else {
            $offset = 0;
            $length = $start;
        }

        $table = new BookEntriesTable($output, $this->bookService);
        $table
            ->setBook($book, $offset, $length)
            ->render();

        return self::SUCCESS;
    }
}
