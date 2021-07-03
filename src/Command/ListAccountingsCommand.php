<?php

namespace App\Command;

use App\Accounting\AccountingLocator;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Helper\Table;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class ListAccountingsCommand extends Command
{
    private $accountingLocator;

    public function __construct(AccountingLocator $accountingLocator)
    {
        parent::__construct();
        $this->accountingLocator = $accountingLocator;
    }

    protected function configure()
    {
        $this->setName('account:list:accountings');
        $this->setAliases(['accountings']);
        $this->setDescription('Get a list of the Accounting classes');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $table = new Table($output);
        $table->setHeaders(['Key', 'Name']);

        foreach ($this->accountingLocator->getAll() as $accounting) {
            $table->addRow([$accounting::getKey(), $accounting::getName()]);
        }

        $table->render();

        return self::SUCCESS;
    }
}
