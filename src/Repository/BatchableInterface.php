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
     * The callable is passed an instance of the query builder as the first param and the alias as the second
     * Use this callable to filter batches.
     *
     * @return object[]
     */
    public function getBatch(Batch $batch, callable $queryBuilderUpdater = null): array;
}
