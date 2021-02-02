<?php

namespace App\Command;

use App\Entity\Entry;
use App\Repository\BookRepository;
use App\Repository\EntryRepository;
use App\Service\BookService;
use DateTime;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Helper\Table;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class RemoveEntryCommand extends Command
{
    /** @var BookRepository */
    private $bookRepository;

    /** @var EntryRepository */
    private $entryRepository;

    /** @var BookService */
    private $bookService;

    public function __construct(
        BookRepository $bookRepository, 
        EntryRepository $entryRepository,
        BookService $bookService
    ){
        parent::__construct();

        $this->bookRepository = $bookRepository;
        $this->entryRepository = $entryRepository;
        $this->bookService = $bookService;
    }

    protected function configure()
    {
        $this->setName('account:erase');
    
        $this->addArgument('name', InputArgument::REQUIRED, 'Book name of the entry');
        $this->addArgument('id', InputArgument::REQUIRED, 'Id of the entry to be removed');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $name = $input->getArgument('name');
        $id = $input->getArgument('id');

        $book = $this->bookRepository->findOneBy(['name' => $name]);
        $entry = $this->entryRepository->find($id);

        if (!$book || !$entry) {
            return self::FAILURE;
        }

        $book->removeEntry($entry);
        $this->bookService->saveBook($book);

        $table = new Table($output);
        $table
            ->setHeaders(['Book', 'Amount', 'Cost', 'Average Cost'])
            ->setRows([[
            $book->getName(),
            $book->getTotalAmount(), 
            $book->getTotalCost(), 
            $book->getAverageCost()
        ]])->render();
        
        return self::SUCCESS;
    }
}
