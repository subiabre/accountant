<?php

namespace App\Component\Table;

use Symfony\Component\Console\Helper\Table;
use Symfony\Component\Console\Output\OutputInterface;

abstract class AbstractTable
{
    private Table $table;

    private array $columns = [];
    private array $items = [];

    protected $row;

    public function __construct(OutputInterface $output)
    {
        $this->table = new Table($output);
    }

    abstract public function configure(): void;

    /**
     * @param string $header Column header title
     * @param string $method Method of this table to be run on each row at this column
     */
    final public function setColumn(string $header, string $method): self
    {
        $this->columns[$header] = $method;

        return $this;
    }

    final public function clearColumns(): self
    {
        $this->columns = [];

        return $this;
    }

    final public function removeColumn(string $header): self
    {
        $this->columns = array_filter(array_diff_key($this->columns, [$header]));

        return $this;
    }

    public function addItem($item): self
    {
        array_push($this->items, $item);

        return $this;
    }

    public function addItems($items): self
    {
        foreach ($items as $item) {
            $this->addItem($item);
        }

        return $this;
    }

    /**
     * This method will be run once before the row items are iterated
     * @param $rows The complete row items array
     */
    abstract protected function beforeRows($rows): void;

    /**
     * This method will be run on each row item before the column methods
     * @param $row The current row item in the iteration
     */
    abstract protected function onRow($row): void;

    final protected function preRender()
    {
        $this->table->setHeaders(array_keys($this->columns));

        $this->beforeRows($this->items);
        foreach ($this->items as $item) {
            $this->onRow($item);
            $this->row = $item;

            $row = [];
            foreach (array_values($this->columns) as $method) {
                $row[] = $this->$method();
            }

            $this->table->addRow($row);
        }
    }

    final public function render()
    {
        $this->configure();
        $this->preRender();

        return $this->table->render();
    }
}