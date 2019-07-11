<?php

declare(strict_types=1);

namespace Setono\DoctrineORMBatcher\Batcher\Range;

use Setono\DoctrineORMBatcher\Batch\RangeBatchInterface;
use Setono\DoctrineORMBatcher\Batcher\BatcherInterface;

interface RangeBatcherInterface extends BatcherInterface
{
    /**
     * @return iterable<RangeBatchInterface>
     */
    public function getBatches(int $batchSize = 100): iterable;
}
