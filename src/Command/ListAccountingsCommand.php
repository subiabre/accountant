<?php

namespace App\Command;

use App\Service\AccountingService;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Helper\Table;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class ListAccountingsCommand extends Command
{
    private $accountingService;

    public function __construct(AccountingService $accountingService)
    {
        parent::__construct();
        $this->accountingService = $accountingService;
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
        $table->setHeaders(['Key', 'Description']);

        foreach ($this->accountingService->getAccountings() as $accounting) {
            $table->addRow([$accounting::getKey(), $accounting::getDescription()]);
        }

        $table->render();

        return self::SUCCESS;
    }
}
