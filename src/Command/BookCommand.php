<?php

namespace App\Command;

use App\Entity\Book;
use App\Repository\BookRepository;
use App\Service\BookService;
use Brick\Money\Context;
use Brick\Money\Context\CustomContext;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;

abstract class BookCommand extends Command
{
    /** @var BookRepository */
    protected $bookRepository;

    /** @var BookService */
    protected $bookService;

    public function __construct(
        BookRepository $bookRepository, 
        BookService $bookService
    ){
        parent::__construct();

        $this->bookRepository = $bookRepository;
        $this->bookService = $bookService;
    }

    protected function getHiddenOption(InputInterface $input, Book $book): bool
    {
        return $input->getOption('hidden') 
            ? filter_var($input->getOption('hidden'), FILTER_VALIDATE_BOOLEAN) 
            : $book->isHidden();
    }

    protected function setHiddenOption(int $inputOption = InputOption::VALUE_OPTIONAL)
    {
        $this->addOption('hidden', null, $inputOption, 'Set this book as hidden', Book::DEFAULT_HIDDEN);
    }

    protected function getContextOption(InputInterface $input, Book $book): Context
    {
        return $input->getOption('context') 
            ? new CustomContext($input->getOption('context')) 
            : $book->getCashContext();
    }

    protected function setContextOption(int $inputOption = InputOption::VALUE_OPTIONAL)
    {
        $this->addOption('context', null, $inputOption, 'Number of decimals to preserve before rounding', Book::DEFAULT_CONTEXT);
    }

    protected function getSortOption(InputInterface $input): string
    {
        return $input->getOption('sort') ? $input->getOption('sort') : Book::SORT_ASCENDING;
    }

    protected function setSortOption(int $inputOption = InputOption::VALUE_OPTIONAL)
    {
        $this->addOption('sort', null, $inputOption, 'Set the sort order (ASC/DESC)', Book::SORT_ASCENDING);
    }
}
