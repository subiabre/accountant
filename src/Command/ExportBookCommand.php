<?php

use App\Command\BookCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class ExportBookCommand extends BookCommand
{
    protected function configure()
    {
        $this->setName('account:export');
        $this->setAliases(['export']);
        $this->setDescription('Export books data');

        $this->addArgument('filename', InputArgument::REQUIRED, 'Name of the generated file');
        $this->addArgument('books', InputArgument::IS_ARRAY, 'Names of the books to be exported');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $filename = $input->getArgument('filename');
        $names = $input->getArgument('books');
        $books = empty($names) ? $this->bookRepository->findAll() : $this->bookRepository->findBy(['name' => $names]);

        $data = json_encode($books, JSON_PRETTY_PRINT);

        file_put_contents($filename, $data);
    }
}
