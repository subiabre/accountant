<?php

namespace App\Service;

use App\Entity\Entry;
use Doctrine\ORM\EntityManagerInterface;

class EntryService
{
    private $em;

    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }

    public function findEntry(int $entryId): ?Entry
    {
        return $this->em->find(Entry::class, $entryId);
    }

    public function isValidEntryType(string $type): bool
    {
        switch ($type) {
            case Entry::BUY:
                return true;
                break;

            case Entry::SELL:
                return true;
                break;
            
            default:
                return false;
                break;
        }
    }
}
