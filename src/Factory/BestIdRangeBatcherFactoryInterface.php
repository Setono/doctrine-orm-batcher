<?php

declare(strict_types=1);

namespace Setono\DoctrineORMBatcher\Factory;

use Doctrine\ORM\QueryBuilder;
use Setono\DoctrineORMBatcher\Batcher\BatcherInterface;

interface BestIdRangeBatcherFactoryInterface
{
    public function create(QueryBuilder $qb, string $identifier = 'id', int $sparsenessThreshold = 5): BatcherInterface;
}
