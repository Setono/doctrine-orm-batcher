<?php

declare(strict_types=1);

namespace Setono\DoctrineORMBatcher\Repository;

use Setono\DoctrineORMBatcher\Batch\Batch;

/**
 * Implement this interface in your repository to make it batchable.
 */
interface BatchableInterface
{
    /**
     * @return object[]
     */
    public function getBatch(Batch $batch): array;
}
