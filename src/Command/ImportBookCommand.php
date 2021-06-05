<?php

namespace App\Command;

use App\Repository\BookRepository;
use App\Service\BookService;
use Doctrine\Common\Annotations\AnnotationReader;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Serializer\Mapping\Factory\ClassMetadataFactory;
use Symfony\Component\Serializer\Mapping\Loader\AnnotationLoader;
use Symfony\Component\Serializer\Normalizer\AbstractNormalizer;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Serializer\Serializer;

class ImportBookCommand extends AbstractBookCommand
{
    /** @var SerializerInterface */
    private $serializer;

    public function __construct(
        BookService $bookService,
        BookRepository $bookRepository
    )
    {
        parent::__construct($bookRepository, $bookService);

        $classMetadataFactory = new ClassMetadataFactory(new AnnotationLoader(new AnnotationReader()));
        $this->serializer = new Serializer([new ObjectNormalizer($classMetadataFactory)]);
    }

    protected function configure()
    {
        $this->setName('account:import');
        $this->setAliases(['import']);
        $this->setDescription('Import books data');

        $this->addArgument('filename', InputArgument::REQUIRED, 'Name of the JSON file');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $filename = rtrim($input->getArgument('filename'), '.json');

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
                $this->serializer->deserialize($data, Book::class, 'json', [
                    AbstractNormalizer::OBJECT_TO_POPULATE => $book
                ]);

                $this->bookService->saveBook($book);
            } else {
                $this->bookService->saveNewBook($this->serializer->deserialize($data, Book::class, 'json'));
            }
        }

        return self::SUCCESS;
    }
}
