<?php

namespace App\Command;

use App\Repository\BookRepository;
use App\Service\BookService;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\ConfirmationQuestion;

class RemoveBookCommand extends Command
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
        $this->setName('account:remove:book');
        $this->setAliases(['drop']);
        $this->setDescription('Delete a book and all the entries it contains');
    
        $this->addArgument('name', InputArgument::REQUIRED, 'Book name');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $name = $input->getArgument('name');

        $book = $this->bookRepository->findOneBy(['name' => $name]);

        if (!$book) {
            $output->writeln("The book `$name` does not exist.");
            return self::FAILURE;
        }

        $totalEntries = count($book->getEntries());
        $output->writeln("The book `$name` contains $totalEntries entries");

        $helper = $this->getHelper('question');
        $question = new ConfirmationQuestion("Do you want to delete this book? (y/n): ", false);

        if (!$helper->ask($input, $output, $question)) {
            return self::SUCCESS;
        }

        $this->bookService->deleteBook($book);

        $output->writeln("The book `$name` was successfully deleted.");
        
        return self::SUCCESS;
    }
}
