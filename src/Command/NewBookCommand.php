<?php

namespace App\Command;

use App\Entity\Book;
use App\Repository\BookRepository;
use App\Service\BookService;
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
        $this->setAliases(['book']);
        $this->setDescription('Create a new accounting book');
    
        $this->addArgument('name', InputArgument::REQUIRED, 'Book name for this entry');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $name = $input->getArgument('name');

        $book = $this->bookRepository->findOneBy(['name' => $name]);

        if ($book) {
            $output->writeln("The book `$name` already exists.");
            return self::FAILURE;
        }

        $book = new Book();
        $book->setName($name);

        $this->bookService->saveBook($book);

        return self::SUCCESS;
    }
}
