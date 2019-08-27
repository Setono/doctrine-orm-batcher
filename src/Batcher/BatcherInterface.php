<?php

declare(strict_types=1);

namespace Setono\DoctrineORMBatcher\Batcher;

use Setono\DoctrineORMBatcher\Batch\BatchInterface;

interface BatcherInterface
{
    /**
     * @return iterable<BatchInterface>
     */
    public function getBatches(int $batchSize = 100): iterable;

    /**
     * Returns the number of batches that will be returned.
     */
    public function getBatchCount(int $batchSize = 100): int;
}
