<?php

namespace App\Command;

use App\Entity\Book;
use App\Repository\BookRepository;
use App\Service\BookService;
use Brick\Money\Context\CustomContext;
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
        $this->setDescription('Create a new accounting book');

        $this->addArgument('name', InputArgument::REQUIRED, 'Book name for this entry');
        $this->addArgument('currency', InputArgument::OPTIONAL, 'Default currency code for entries in this book', 'USD');
        $this->addArgument('rounding', InputArgument::OPTIONAL, 'Number of decimals to preserve before rounding', 2);
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $name = $input->getArgument('name');
        $currency = Currency::of($input->getArgument('currency'));
        $context = new CustomContext($input->getArgument('rounding'));

        $book = $this->bookRepository->findOneBy(['name' => $name]);

        if ($book) {
            $output->writeln("The book `$name` already exists.");
            return self::FAILURE;
        }

        $book = new Book();
        $book->setName($name);
        $book->setCurrency($currency);
        $book->setCashContext($context);

        $this->bookService->saveNewBook($book);

        $output->writeln("The book `$name` was successfully created.");

        return self::SUCCESS;
    }
}
