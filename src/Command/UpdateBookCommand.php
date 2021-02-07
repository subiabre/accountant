<?php

namespace App\Command;

use App\Repository\BookRepository;
use App\Service\BookService;
use Brick\Money\Context\CustomContext;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
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
        $this->addArgument('rounding', InputArgument::OPTIONAL, 'Number of decimals to preserve before rounding', 2);
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $name = $input->getArgument('name');
        $context = new CustomContext($input->getArgument('rounding'));

        $book = $this->bookRepository->findOneBy(['name' => $name]);

        if (!$book) {
            $output->writeln(sprintf(BookService::BOOK_MISSING, $name));
            return self::FAILURE;
        }

        $book->setCashContext($context);

        $this->bookService->saveBook($book);

        $output->writeln(sprintf(BookService::BOOK_UPDATED, $name));

        return self::SUCCESS;
    }
}
