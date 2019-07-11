<?php

declare(strict_types=1);

namespace Setono\DoctrineORMBatcher\Batcher\Range;

interface NaiveIdRangeBatcherInterface extends RangeBatcherInterface
{
    /**
     * Returns an indication (from 1-100) about how sparse the batches will be.
     */
    public function getSparseness(): int;
}
