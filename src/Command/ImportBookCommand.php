<?php

namespace App\Command;

use App\Entity\Book;
use App\Entity\Entry;
use Brick\Money\Context\CustomContext;
use Brick\Money\Currency;
use Brick\Money\Money;
use DateTime;
use DateTimeZone;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\ConfirmationQuestion;

class ImportBookCommand extends AbstractBookCommand
{
    protected function configure()
    {
        $this->setName('account:import');
        $this->setAliases(['import']);
        $this->setDescription('Import books data from an accountant generated JSON file');

        $this->addArgument('filename', InputArgument::REQUIRED, 'Name of the JSON file');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $filename = sprintf('%s.json', rtrim($input->getArgument('filename'), '.json'));

        if (!file_exists($filename)) {
            $output->writeln(sprintf('<error>The file %s does not exist.</error>', $filename));
            return self::FAILURE;
        }

        $file = json_decode(file_get_contents($filename), true);

        if (!$file || empty($file)) {
            $output->writeln(sprintf('<error>The file %s does not contain valid data.</error>', $filename));
            return self::FAILURE;
        }
        
        foreach ($file as $data) {
            $book = $this->bookService->findBookByName($data['name']);
            
            if ($book) {
                $output->writeln(sprintf(Book::MESSAGE_ALREADY, $book->getName()));

                $helper = $this->getHelper('question');
                $question = new ConfirmationQuestion('Do you want to overwrite it? (y/n): ', false);

                if ($helper->ask($input, $output, $question)) {
                    $this->bookService->deleteBook($book);
                    $this->bookService->saveNewBook($this->processBookData($data, $book));
    
                    $output->writeln(sprintf(Book::MESSAGE_UPDATED, $data['name']));
                } else {
                    continue;
                }
            } else {
                $this->bookService->saveNewBook($this->processBookData($data, new Book));

                $output->writeln(sprintf(Book::MESSAGE_CREATED, $data['name']));
            }
        }

        return self::SUCCESS;
    }

    private function processBookData(array $data, Book $book): Book
    {
        $book
            ->setName($data['name'])
            ->setCurrency(Currency::of($data['currency']['currencyCode']))
            ->setIsHidden($data['hidden'])
            ->setCashContext(new CustomContext(
                    $data['cashContext']['scale'], 
                    $data['cashContext']['step'])
                )
            ->setCashFormat($data['cashFormat'])
            ->setDateFormat($data['dateFormat'])
            ;

        foreach ($data['entries'] as $entryData) {
            $entry = new Entry();
            $entry
                ->setType(strval($entryData['type']))
                ->setDate(
                    DateTime::createFromFormat(
                        'U',
                        $entryData['date']['timestamp'],
                        new DateTimeZone($entryData['date']['timezone']['name'])
                    ))
                ->setAmount(floatval($entryData['amount']))
                ->setValue(
                    Money::of(
                        floatval(sprintf('%d.%d', 
                            $entryData['cost']['amount']['integralPart'],
                            $entryData['cost']['amount']['fractionalPart']
                        )), 
                        Currency::of($data['currency']['currencyCode'])
                    ))
                ;

            $book = $this->bookService->addEntry($entry, $book);
        }

        return $book;
    }
}
