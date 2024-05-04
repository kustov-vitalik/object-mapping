<?php

declare(strict_types=1);

namespace VKPHPUtils\Mapping\DS;

use IteratorAggregate;
use ArrayAccess;
use Countable;
use RuntimeException;
use Iterator;
use Traversable;

/**
 * @template            TKey
 * @template            TValue
 * @template-implements IteratorAggregate<TKey, TValue>
 * @template-implements ArrayAccess<TKey, TValue>
 */
class Map implements ArrayAccess, Countable, IteratorAggregate
{
    /**
     * @var list<TKey> 
     */
    private array $keys = [];

    /**
     * @var list<TValue> 
     */
    private array $values = [];

    /**
     * @param TKey   $key
     * @param TValue $value
     */
    public function put(mixed $key, mixed $value): void
    {
        $keyIndex = array_search($key, $this->keys, false);
        if ($keyIndex === false) {
            $this->keys[] = $key;
            $keyIndex = array_search($key, $this->keys, false);
        }

        $this->values[$keyIndex] = $value;
    }

    /**
     * @param TKey $key
     */
    public function remove(mixed $key): void
    {
        $keyIndex = array_search($key, $this->keys, false);
        if ($keyIndex !== false) {
            unset($this->keys[$keyIndex], $this->values[$keyIndex]);
        }
    }

    /**
     * @param TKey $key
     */
    public function containsKey(mixed $key): bool
    {
        return in_array($key, $this->keys, false);
    }

    /**
     * @param  TKey $key
     * @return TValue
     */
    public function get(mixed $key): mixed
    {
        $keyIndex = array_search($key, $this->keys, false);
        if ($keyIndex === false) {
            throw new RuntimeException(
                sprintf("Key does not exist: %s. Map: %s", print_r($key, true), print_r($this, true))
            );
        }

        return $this->values[$keyIndex];
    }

    /**
     * @return list<TKey>
     */
    public function keys(): array
    {
        return $this->keys;
    }

    /**
     * @return list<TValue>
     */
    public function values(): array
    {
        return $this->values;
    }

    /**
     * @param TKey $offset
     */
    public function offsetExists(mixed $offset): bool
    {
        return $this->containsKey($offset);
    }

    /**
     * @param  TKey $offset
     * @return TValue
     */
    public function offsetGet(mixed $offset): mixed
    {
        return $this->get($offset);
    }

    /**
     * @param TKey   $offset
     * @param TValue $value
     */
    public function offsetSet(mixed $offset, mixed $value): void
    {
        $this->put($offset, $value);
    }

    /**
     * @param TKey $offset
     */
    public function offsetUnset(mixed $offset): void
    {
        $this->remove($offset);
    }

    public function count(): int
    {
        return count($this->keys);
    }

    public function isEmpty(): bool
    {
        return count($this) === 0;
    }

    /**
     * @return Iterator<TKey, TValue>
     */
    public function getIterator(): Iterator
    {
        return new class($this->keys, $this->values) implements Iterator {

            private int $position = 0;

            private readonly array $keys;

            private readonly array $values;

            public function __construct(array $keys, array $values)
            {
                $this->keys = array_values($keys);
                $this->values = array_values($values);
            }

            public function current(): mixed
            {
                return $this->values[$this->position];
            }

            public function next(): void
            {
                ++$this->position;
            }

            public function key(): mixed
            {
                return $this->keys[$this->position];
            }

            public function valid(): bool
            {
                return isset($this->keys[$this->position]);
            }

            public function rewind(): void
            {
                $this->position = 0;
            }
        };
    }
}
