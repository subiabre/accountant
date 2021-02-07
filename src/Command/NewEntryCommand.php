<?php

namespace App\Command;

use App\Entity\Entry;
use App\Repository\BookRepository;
use App\Service\BookService;
use App\Table\BookEntriesTable;
use Brick\Money\Currency;
use Brick\Money\Money;
use DateTime;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class NewEntryCommand extends Command
{
    /** @var BookRepository */
    private $bookRepository;

    /** @var BookService */
    private $bookService;

    public function __construct(
        BookRepository $bookRepository, 
        BookService $bookService
    ){
        parent::__construct();

        $this->bookRepository = $bookRepository;
        $this->bookService = $bookService;
    }

    protected function configure()
    {
        $this->setName('account:new:entry');
        $this->setAliases(['add']);
        $this->setDescription('Add a new entry to a book');
    
        $this->addArgument('name', InputArgument::REQUIRED, 'Book name for this entry');
        $this->addArgument('amount', InputArgument::REQUIRED, 'Amount value of this entry');
        $this->addArgument('cost', InputArgument::REQUIRED, 'Cost value of this entry');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $name = $input->getArgument('name');
        $book = $this->bookRepository->findOneBy(['name' => $name]);

        if (!$book) {
            $output->writeln(sprintf(BookService::BOOK_MISSING, $name));
            return self::FAILURE;
        }

        $amount = floatval($input->getArgument('amount'));
        $cost = Money::of($input->getArgument('cost'), $book->getCurrency());

        $entry = new Entry();
        $entry->setDate(new DateTime());
        $entry->setAmount($amount);
        $entry->setCost($cost);

        $book = $this->bookService->addEntry($entry, $book);

        $this->bookService->saveBook($book);

        $table = new BookEntriesTable($output, $this->bookService);
        $table
            ->setBook($book, -1)
            ->render();
        
        return self::SUCCESS;
    }
}
