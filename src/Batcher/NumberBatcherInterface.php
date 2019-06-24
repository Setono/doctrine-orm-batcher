<?php

namespace Setono\DoctrineORMBatch\Batcher;

interface NumberBatcherInterface
{
    public function getBatches(int $min, int $max, int $batchSize = 100): iterable;
}
