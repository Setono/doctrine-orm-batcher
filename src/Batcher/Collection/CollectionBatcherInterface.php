<?php

declare(strict_types=1);

namespace Setono\DoctrineORMBatcher\Batcher\Collection;

use Setono\DoctrineORMBatcher\Batch\CollectionBatchInterface;
use Setono\DoctrineORMBatcher\Batcher\BatcherInterface;

interface CollectionBatcherInterface extends BatcherInterface
{
    /**
     * @return iterable<CollectionBatchInterface>
     */
    public function getBatches(int $batchSize = 100): iterable;
}
