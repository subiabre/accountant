<?php

namespace App\Command;

use App\Command\BookCommand;
use App\Repository\BookRepository;
use App\Service\BookService;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Serializer\SerializerInterface;

class ExportBookCommand extends BookCommand
{
    /** @var SerializerInterface */
    private $serializer;

    public function __construct(
        SerializerInterface $serializer,
        BookService $bookService,
        BookRepository $bookRepository
    )
    {
        parent::__construct($bookRepository, $bookService);

        $this->serializer = $serializer;
    }

    protected function configure()
    {
        $this->setName('account:export');
        $this->setAliases(['export']);
        $this->setDescription('Export books data');

        $this->addArgument('filename', InputArgument::REQUIRED, 'Name of the generated file');
        $this->addArgument('books', InputArgument::IS_ARRAY, 'Names of the books to be exported', []);
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $filename = $input->getArgument('filename');
        $names = $input->getArgument('books');

        /** @var Book[] */
        $books = empty($names) ? $this->bookRepository->findAll() : $this->bookRepository->findBy(['name' => $names]);

        file_put_contents($filename, $this->serializer->serialize($books, 'json', ['groups' => 'default']));

        return self::SUCCESS;
    }
}
