<?php

namespace App\Command;

use App\Repository\BookRepository;
use App\Service\BookService;
use App\Table\BookEntriesTable;
use App\Table\BooksTable;
use Brick\Money\Context\CustomContext;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
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

        $this->addOption('rounding', null, InputOption::VALUE_OPTIONAL, 'Number of decimals to preserve before rounding');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $name = $input->getArgument('name');
        $start = (int) $input->getArgument('max');

        if (!$name) {
            $books = $this->bookRepository->findBy(['isHidden' => false], ['name' => 'DESC']);

            $table = new BooksTable($output);
            $table
                ->setBooks($books)
                ->render();

            return self::SUCCESS;
        }

        $book = $this->bookRepository->findOneBy(['name' => $name]);
        $context = $input->getOption('rounding') ? new CustomContext($input->getOption('rounding')) : $book->getCashContext();

        if (!$book) {
            $output->writeln(sprintf(BookService::BOOK_MISSING, $name));
            return self::FAILURE;
        }

        if ($start < 0) {
            $offset = $start;
            $length = null;
        } else {
            $offset = 0;
            $length = $start;
        }

        $book->setCashContext($context);

        $table = new BookEntriesTable($output, $this->bookService);
        $table
            ->setBook($book, $offset, $length)
            ->render();

        return self::SUCCESS;
    }
}
