<?php

declare(strict_types=1);

namespace Setono\DoctrineORMBatcher\Factory;

use Doctrine\ORM\QueryBuilder;
use Setono\DoctrineORMBatcher\Batcher\BatcherInterface;
use Setono\DoctrineORMBatcher\Batcher\Range\BestIdRangeBatcher;
use Setono\DoctrineORMBatcher\Batcher\Range\IdRangeBatcher;
use Setono\DoctrineORMBatcher\Batcher\Range\NaiveIdBatcher;
use Setono\DoctrineORMBatcher\Batcher\Range\NumberBatcher;

final class BestIdRangeBatcherFactory implements BestIdRangeBatcherFactoryInterface
{
    public function create(QueryBuilder $qb, string $identifier = 'id', int $sparsenessThreshold = 5): BatcherInterface
    {
        $naiveIdBatcher = new NaiveIdBatcher($qb, $identifier, new NumberBatcher());
        $realIdBatcher = new IdRangeBatcher($qb, $identifier);

        return new BestIdRangeBatcher($naiveIdBatcher, $realIdBatcher, $sparsenessThreshold);
    }
}
