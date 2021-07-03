<?php

namespace App\Console;

use App\Accounting\AccountingLocator;
use App\Entity\Book;
use App\Repository\BookRepository;
use App\Service\BookService;
use App\Service\EntryService;
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
    public const MESSAGE_ARGUMENT_ACCOUNTING = 'Accounting key for the method in this book';
    public const MESSAGE_ARGUMENT_CURRENCY = 'Currency code for the currency used in entries in this book';

    /** @var BookRepository */
    protected $bookRepository;

    /** @var BookService */
    protected $bookService;

    /** @var EntryService */
    protected $entryService;

    /** @var AccountingLocator */
    protected $accountingLocator;

    public function __construct(
        BookRepository $bookRepository, 
        BookService $bookService,
        EntryService $entryService,
        AccountingLocator $accountingLocator
    ){
        parent::__construct();

        $this->bookRepository = $bookRepository;
        $this->bookService = $bookService;
        $this->entryService = $entryService;
        $this->accountingLocator = $accountingLocator;
    }

    protected function parseAccounting($input)
    {
        return $this->accountingLocator->getByKey($input->getArgument('accounting'))
        ? $this->accountingLocator->getByKey($input->getArgument('accounting'))
        : $this->accountingLocator->getByKey(Book::DEFAULT_ACCOUNTING_KEY)
        ;
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
        if (!$input->getParameterOption('--cash-context')) {
            return $book->getCashContext() ? $book->getCashContext() : new CustomContext(Book::DEFAULT_CASH_CONTEXT);
        } else {
            return new CustomContext($input->getOption('cash-context'));
        }
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
        if (!$input->getParameterOption('--cash-format')) {
            return $book->getCashFormat() ? $book->getCashFormat() : Book::DEFAULT_CASH_FORMAT;
        } else {
            return $input->getOption('cash-format');
        }
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

    protected function getDateFormatOption(InputInterface $input, Book $book): string
    {
        if (!$input->getParameterOption('--date-format')) {
            return $book->getDateFormat() ? $book->getDateFormat() : Book::DEFAULT_DATE_FORMAT;
        } else {
            return $input->getOption('date-format');
        }
    }

    protected function setDateFormatOption(int $inputOption = InputOption::VALUE_OPTIONAL)
    {
        $this->addOption(
            'date-format',
            null,
            $inputOption,
            'Format to display the dates',
            Book::DEFAULT_DATE_FORMAT
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
