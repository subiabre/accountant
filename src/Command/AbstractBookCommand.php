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

/**
 * Base class for book commands
 */
abstract class AbstractBookCommand extends Command
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
        $this->addOption(
            'hidden', 
            null, 
            $inputOption, 
            'Set this book as hidden', 
            Book::DEFAULT_HIDDEN
        );
    }

    protected function getCashContextOption(InputInterface $input, Book $book): Context
    {
        return $input->getOption('cash-context') 
            ? new CustomContext($input->getOption('cash-context')) 
            : $book->getCashContext();
    }

    protected function setCashContextOption(int $inputOption = InputOption::VALUE_OPTIONAL)
    {
        $this->addOption(
            'cash-context', 
            null, 
            $inputOption, 
            'Number of decimals to preserve before rounding', 
            Book::DEFAULT_CASH_CONTEXT
        );
    }

    protected function getCashFormatOption(InputInterface $input, Book $book): string
    {
        return $input->getOption('cash-format')
            ? $input->getOption('cash-format')
            : $book->getCashFormat();
    }

    protected function setCashFormatOption(int $inputOption = InputOption::VALUE_OPTIONAL)
    {
        $this->addOption(
            'cash-format', 
            null, 
            $inputOption, 
            'Format to display the currency', 
            Book::DEFAULT_CASH_FORMAT
        );
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
