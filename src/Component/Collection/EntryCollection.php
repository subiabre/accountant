<?php

namespace App\Component\Collection;

use App\Entity\Entry;
use Doctrine\Common\Collections\ArrayCollection;

class EntryCollection extends ArrayCollection
{
    private $elements;

    private function getByType(string $type): array
    {
        $result = [];

        foreach ($this->elements as $entry) {
            if ($entry->getType() === $type) {
                $result[] = $entry;
            }
        }

        return $result;
    }

    /**
     * @return Entry[]
     */
    public function getSells(): array
    {
        return $this->getByType(Entry::SELL);
    }

    /**
     * @return Entry[]
     */
    public function getBuys(): array
    {
        return $this->getByType(Entry::BUY);
    }
}
