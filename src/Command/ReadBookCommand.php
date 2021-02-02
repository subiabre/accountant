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
        $this->addArgument('max', InputArgument::OPTIONAL, 'Max number of entries to print');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $name = $input->getArgument('name');
        $start = (int) $input->getArgument('max');

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

        $bookTable = new Table($output);
        $bookTable
            ->setHeaders([
                'Book',
                'Entry Id',
                'Entry Amount',
                'Entry Cost',
                'Total Amount',
                'Total Cost',
                'Average Cost'
            ])
            ->setRows($this->bookService->readEntries($book, $offset, $length))
            ->render();

        return self::SUCCESS;
    }
}
