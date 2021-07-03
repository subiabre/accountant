<?php

namespace App\Component\Collection;

use App\Entity\Entry;
use Closure;
use Doctrine\Common\Collections\Collection;

class EntryCollection implements Collection, EntryCollectionInterface
{
    /** @var Collection */
    private $collection;

    public function __construct(Collection $collection)
    {
        $this->collection = $collection;
    }

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

    public function get($key)
    {
        return $this->collection->get($key);
    }

    public function getValues()
    {
        return $this->collection->getValues();
    }

    public function set($key, $value)
    {
        return $this->collection->set($key, $value);
    }

    public function add($element)
    {
        return $this->collection->add($element);
    }

    public function clear()
    {
        return $this->collection->clear();
    }

    public function contains($element)
    {
        return $this->collection->contains($element);
    }

    public function exists(Closure $p)
    {
        return $this->collection->exists($p);
    }

    public function filter(Closure $p)
    {
        return $this->collection->filter($p);
    }

    public function forAll(Closure $p)
    {
        return $this->collection->forAll($p);
    }

    public function map(Closure $func)
    {
        return $this->collection->map($func);
    }

    public function partition(Closure $p)
    {
        return $this->collection->partition($p);
    }

    public function indexOf($element)
    {
        return $this->collection->indexOf($element);
    }

    public function count()
    {
        return $this->collection->count();
    }

    public function slice($offset, $length = null)
    {
        return $this->collection->slice($offset, $length);
    }

    public function isEmpty()
    {
        return $this->collection->isEmpty();
    }

    public function remove($key)
    {
        return $this->collection->remove($key);
    }

    public function removeElement($element)
    {
        return $this->collection->removeElement($element);
    }

    public function containsKey($key)
    {
        return $this->collection->containsKey($key);
    }

    public function getKeys()
    {
        return $this->collection->getKeys();
    }

    public function key()
    {
        return $this->collection->key();
    }

    public function first()
    {
        return $this->collection->first();
    }

    public function current()
    {
        return $this->collection->current();
    }

    public function next()
    {
        return $this->collection->next();
    }

    public function last()
    {
        return $this->collection->last();
    }

    public function toArray()
    {
        return $this->collection->toArray();
    }

    public function getIterator()
    {
        return $this->collection->getIterator();
    }

    public function offsetExists($offset)
    {
        return $this->offsetExists($offset);
    }

    public function offsetGet($offset)
    {
        return $this->collection->offsetGet($offset);
    }

    public function offsetSet($offset, $value)
    {
        return $this->collection->offsetSet($offset, $value);
    }

    public function offsetUnset($offset)
    {
        return $this->collection->offsetUnset($offset);
    }
}
