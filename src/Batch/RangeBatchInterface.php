<?php

declare(strict_types=1);

namespace Setono\DoctrineORMBatcher\Batch;

interface RangeBatchInterface extends BatchInterface
{
    public function getLowerBound(): int;

    public function getUpperBound(): int;
}
