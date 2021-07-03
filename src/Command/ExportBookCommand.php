<?php

namespace App\Command;

use App\Console\AbstractBookCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Normalizer\DateTimeNormalizer;
use Symfony\Component\Serializer\Normalizer\JsonSerializableNormalizer;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Serializer\Serializer;

class ExportBookCommand extends AbstractBookCommand
{
    protected function configure()
    {
        $this->setName('account:export');
        $this->setAliases(['export']);
        $this->setDescription('Export books data to a JSON encoded file');

        $this->addArgument('filename', InputArgument::REQUIRED, 'Name of the generated file');
        $this->addArgument('books', InputArgument::IS_ARRAY, 'Names of the books to be exported', []);
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $filename = sprintf('%s.json', rtrim($input->getArgument('filename'), '.json'));
        $names = $input->getArgument('books');

        /** @var Book[] */
        $books = empty($names) ? $this->bookRepository->findAll() : $this->bookRepository->findBy(['name' => $names]);

        $normalizers = [ 
            new ObjectNormalizer(), 
            new DateTimeNormalizer(),
            new JsonSerializableNormalizer()
        ];
        $serializer = new Serializer($normalizers, [new JsonEncoder()]);
        file_put_contents($filename, $serializer->serialize($books, 'json', ['groups' => 'default']));

        return self::SUCCESS;
    }
}
