<?php

namespace Setono\DoctrineORMBatcher\Batcher;

use Setono\DoctrineORMBatcher\Batch\Batch;

interface IdBatcherInterface
{
    /**
     * @return iterable|Batch[]
     */
    public function getBatches(int $batchSize = 100): iterable;
}
