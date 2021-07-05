<?php

namespace App\Command;

use App\Console\AbstractBookCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Normalizer\AbstractNormalizer;
use Symfony\Component\Serializer\Normalizer\DateTimeNormalizer;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Serializer\Serializer;

class ExportBookCommand extends AbstractBookCommand
{
    protected function configure()
    {
        $this->setName('commands:export');
        $this->setAliases(['export']);
        $this->setDescription('Export books data to a JSON encoded file');

        $this->addArgument('filename', InputArgument::REQUIRED, 'Name of the generated file');
        $this->addArgument('books', InputArgument::IS_ARRAY, 'Names of the books to be exported', []);
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $filename = $this->normalizeFilename($input->getArgument('filename'));
        $names = $input->getArgument('books');

        /** @var Book[] */
        $books = empty($names) ? $this->bookRepository->findAll() : $this->bookRepository->findBy(['name' => $names]);

        $serializer = new Serializer($this->getNormalizers(), [new JsonEncoder()]);
        file_put_contents($filename, $serializer->serialize($books, 'json', ['groups' => 'default']));

        return self::SUCCESS;
    }

    private function normalizeFilename(string $filename): string
    {
        $filename = preg_replace('/[^a-z0-9\._-]+/', '_', trim($filename));

        return sprintf('%s.json', preg_replace('/.json$/', '', $filename));
    }

    private function getNormalizers(): array
    {
        return [
            new DateTimeNormalizer(),
            new ObjectNormalizer(null, null, null, null, null, null, [
                AbstractNormalizer::CIRCULAR_REFERENCE_HANDLER => function ($object, $format, $context) {
                    return $object->getId();
                }
            ])
        ];
    }
}
