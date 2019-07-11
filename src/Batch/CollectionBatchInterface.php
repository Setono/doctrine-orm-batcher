<?php

declare(strict_types=1);

namespace Setono\DoctrineORMBatcher\Batch;

interface CollectionBatchInterface extends BatchInterface
{
    public function getCollection(): array;
}
