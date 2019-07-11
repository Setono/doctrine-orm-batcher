<?php

declare(strict_types=1);

namespace Setono\DoctrineORMBatcher\Factory;

use Doctrine\ORM\QueryBuilder;
use Setono\DoctrineORMBatcher\Batcher\BatcherInterface;
use Setono\DoctrineORMBatcher\Batcher\Collection\IdCollectionBatcher;
use Setono\DoctrineORMBatcher\Batcher\Collection\ObjectCollectionBatcher;
use Setono\DoctrineORMBatcher\Batcher\Range\BestIdRangeBatcher;
use Setono\DoctrineORMBatcher\Batcher\Range\IdRangeBatcher;
use Setono\DoctrineORMBatcher\Batcher\Range\NaiveIdRangeBatcher;

final class BatcherFactory implements BatcherFactoryInterface
{
    public function createObjectCollectionBatcher(
        QueryBuilder $qb,
        string $identifier = 'id',
        bool $clearOnBatch = true
    ): BatcherInterface {
        return new ObjectCollectionBatcher($qb, $identifier, $clearOnBatch);
    }

    public function createIdCollectionBatcher(
        QueryBuilder $qb,
        string $identifier = 'id',
        bool $clearOnBatch = true
    ): BatcherInterface {
        return new IdCollectionBatcher($qb, $identifier, $clearOnBatch);
    }

    public function createBestIdRangeBatcher(
        QueryBuilder $qb,
        string $identifier = 'id',
        bool $clearOnBatch = true,
        int $sparsenessThreshold = 5
    ): BatcherInterface {
        $naiveIdBatcher = new NaiveIdRangeBatcher($qb, $identifier, $clearOnBatch);
        $realIdBatcher = new IdRangeBatcher($qb, $identifier, $clearOnBatch);

        return new BestIdRangeBatcher($naiveIdBatcher, $realIdBatcher, $sparsenessThreshold);
    }
}
