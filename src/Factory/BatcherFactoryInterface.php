<?php

declare(strict_types=1);

namespace Setono\DoctrineORMBatcher\Factory;

use Doctrine\ORM\QueryBuilder;
use Setono\DoctrineORMBatcher\Batcher\BatcherInterface;

interface BatcherFactoryInterface
{
    public function createIdCollectionBatcher(QueryBuilder $qb, string $identifier = 'id', bool $clearOnBatch = true): BatcherInterface;

    public function createObjectCollectionBatcher(QueryBuilder $qb, string $identifier = 'id', bool $clearOnBatch = true): BatcherInterface;

    public function createBestIdRangeBatcher(QueryBuilder $qb, string $identifier = 'id', bool $clearOnBatch = true, int $sparsenessThreshold = 5): BatcherInterface;
}
