<?php

namespace App\Command;

use App\Entity\Entry;
use App\Repository\BookRepository;
use App\Service\BookService;
use DateTime;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Helper\Table;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class NewEntryCommand extends Command
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
        $this->setName('account:new');
    
        $this->addArgument('name', InputArgument::REQUIRED, 'Book name for this entry');
        $this->addArgument('amount', InputArgument::REQUIRED, 'Amount value of this entry');
        $this->addArgument('cost', InputArgument::REQUIRED, 'Cost value of this entry');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $name = $input->getArgument('name');
        $amount = $input->getArgument('amount');
        $cost = $input->getArgument('cost');

        $book = $this->bookRepository->findOneBy(['name' => $name]);

        if (!$book) {
            $output->writeln("The book `$name` does not exist.");
            return self::FAILURE;
        }

        $entry = new Entry();
        $entry->setDate(new DateTime());
        $entry->setAmount($amount);
        $entry->setCost($cost);

        $book = $this->bookService->addEntry($entry, $book);

        $this->bookService->saveBook($book);

        $bookTable = new Table($output);
        $bookTable
            ->setHeaders([
                'Book',
                'Last Entry',
                'Entry Amount',
                'Entry Cost',
                'Total Amount',
                'Total Cost',
                'Average Cost'
            ])
            ->setRows($this->bookService->readEntries($book, -1))
            ->render();
        
        return self::SUCCESS;
    }
}
