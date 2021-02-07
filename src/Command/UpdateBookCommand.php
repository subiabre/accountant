<?php

namespace App\Command;

use App\Repository\BookRepository;
use App\Service\BookService;
use Brick\Money\Context;
use Brick\Money\Context\CustomContext;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class UpdateBookCommand extends Command
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
        $this->setName('account:update:book');
        $this->setAliases(['update']);
        $this->setDescription('Update an accounting book');

        $this->addArgument('name', InputArgument::REQUIRED, 'Book name');

        $this->addOption('rounding', null, InputOption::VALUE_OPTIONAL, 'Number of decimals to preserve before rounding', 2);
        $this->addOption('hidden', null, InputOption::VALUE_OPTIONAL, 'Set this book as hidden', false);
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $name = $input->getArgument('name');
        $book = $this->bookRepository->findOneBy(['name' => $name]);

        if (!$book) {
            $output->writeln(sprintf(BookService::BOOK_MISSING, $name));
            return self::FAILURE;
        }

        $book->setCashContext($this->getContext($input, $book));
        $book->setIsHidden($this->getHidden($input, $book));

        $this->bookService->saveBook($book);

        $output->writeln(sprintf(BookService::BOOK_UPDATED, $name));

        return self::SUCCESS;
    }

    private function getContext($input, $book): Context
    {
        return $input->getOption('rounding') ? new CustomContext($input->getOption('rounding')) : $book->getCashContext();
    }

    private function getHidden($input, $book): bool
    {
        return $input->getOption('hidden') 
            ? filter_var($input->getOption('hidden'), FILTER_VALIDATE_BOOLEAN) 
            : $book->isHidden();
    }
}
