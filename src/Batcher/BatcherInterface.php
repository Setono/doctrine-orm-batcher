<?php

declare(strict_types=1);

namespace Setono\DoctrineORMBatcher\Batcher;

interface BatcherInterface
{
    public function getBatches(int $batchSize = 100): iterable;
}
