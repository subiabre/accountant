<?php

namespace App\Command;

use App\Entity\Book;
use App\Repository\BookRepository;
use App\Service\BookService;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Helper\Table;
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
        $this->setName('account:read');
    
        $this->addArgument('name', InputArgument::REQUIRED, 'Book name to be read');
        $this->addArgument('start', InputArgument::OPTIONAL, 'Max number of entries to print');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $name = $input->getArgument('name');
        $start = $input->getArgument('start');

        $book = $this->bookRepository->findOneBy(['name' => $name]);

        if (!$book) {
            return self::FAILURE;
        }

        $bookTable = new Table($output);
        $bookTable
            ->setHeaders([
                'Book',
                'Entry',
                'Entry Amount',
                'Entry Cost',
                'Total Amount',
                'Total Cost',
                'Average Cost'
            ])
            ->setRows($this->bookService->readEntries($book, $start))
            ->render();

        return self::SUCCESS;
    }
}
