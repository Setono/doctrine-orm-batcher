<?php

declare(strict_types=1);

namespace Setono\DoctrineORMBatcher\Batcher;

use Setono\DoctrineORMBatcher\Batch\Batch;

interface IdBatcherInterface
{
    /**
     * @return iterable|Batch[]
     */
    public function getBatches(int $batchSize = 100): iterable;

    /**
     * Returns an indication (from 1-100) about how sparse the batches will be.
     */
    public function getSparseness(): int;
}
