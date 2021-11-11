<?php

declare(strict_types=1);

namespace Setono\DoctrineORMBatcher\Factory;

use Doctrine\ORM\QueryBuilder;
use Setono\DoctrineORMBatcher\Batcher\Collection\CollectionBatcherInterface;

interface BatcherFactoryInterface
{
    public function createIdCollectionBatcher(QueryBuilder $qb, string $identifier = 'id', bool $clearOnBatch = true): CollectionBatcherInterface;

    public function createObjectCollectionBatcher(QueryBuilder $qb, string $identifier = 'id', bool $clearOnBatch = true): CollectionBatcherInterface;
}
