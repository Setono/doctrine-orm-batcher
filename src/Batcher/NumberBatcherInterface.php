<?php

declare(strict_types=1);

namespace Setono\DoctrineORMBatcher\Batcher;

interface NumberBatcherInterface
{
    public function getBatches(int $min, int $max, int $batchSize = 100): iterable;
}
