<?php

declare(strict_types=1);

namespace Setono\DoctrineORMBatcher\Batch;

final class CollectionBatch
{
    /** @var array */
    private $collection;

    public function __construct(array $collection)
    {
        $this->collection = $collection;
    }

    public function getCollection(): array
    {
        return $this->collection;
    }
}
