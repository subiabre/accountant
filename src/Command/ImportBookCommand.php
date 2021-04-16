<?php

namespace App\Command;

use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class ImportBookCommand extends AbstractBookCommand
{
    protected function configure()
    {
        $this->setName('account:import');
        $this->setAliases(['import']);
        $this->setDescription('Import books data');

        $this->addArgument('filename', InputArgument::REQUIRED, 'Name of the JSON file');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $filename = $input->getArgument('filename');

        $data = json_decode(file_get_contents($filename), true);
    }
}
