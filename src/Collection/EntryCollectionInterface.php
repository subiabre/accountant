<?php

namespace App\Collection;

use Doctrine\Common\Collections\Collection;

interface EntryCollectionInterface
{
    /**
     * @return Entry[]
     */
    public function getSells(): array;

    /**
     * @return Entry[]
     */
    public function getBuys(): array;
}
