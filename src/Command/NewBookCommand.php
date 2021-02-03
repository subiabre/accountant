<?php

namespace App\Command;

use App\Entity\Book;
use App\Repository\BookRepository;
use App\Service\BookService;
use Brick\Money\Currency;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class NewBookCommand extends Command
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
        $this->setName('account:new:book');
        $this->setAliases(['new']);
        $this->setDescription('Create a new accounting book. Books are supposed to hold amounts of the same item.');
    
        $this->addArgument('name', InputArgument::REQUIRED, 'Book name for this entry');
        $this->addArgument('currency', InputArgument::OPTIONAL, 'Default currency code for entries in this book', 'USD');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $name = $input->getArgument('name');
        $currency = Currency::of($input->getArgument('currency'));

        $book = $this->bookRepository->findOneBy(['name' => $name]);

        if ($book) {
            $output->writeln("The book `$name` already exists.");
            return self::FAILURE;
        }

        $book = new Book();
        $book->setName($name);
        $book->setCurrency($currency);

        $this->bookService->saveNewBook($book);

        $output->writeln("The book `$name` was successfully created.");

        return self::SUCCESS;
    }
}
