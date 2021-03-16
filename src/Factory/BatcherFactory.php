<?php

declare(strict_types=1);

namespace Setono\DoctrineORMBatcher\Factory;

use Doctrine\ORM\QueryBuilder;
use Setono\DoctrineORMBatcher\Batcher\Collection\CollectionBatcherInterface;
use Setono\DoctrineORMBatcher\Batcher\Range\NaiveIdRangeBatcherInterface;
use Setono\DoctrineORMBatcher\Batcher\Range\RangeBatcherInterface;

final class BatcherFactory implements BatcherFactoryInterface
{
    private string $objectCollectionBatcherClass;

    private string $idCollectionBatcherClass;

    private string $naiveIdRangeBatcherClass;

    private string $idRangeBatcherClass;

    public function __construct(
        string $objectCollectionBatcherClass,
        string $idCollectionBatcherClass,
        string $naiveIdRangeBatcherClass,
        string $idRangeBatcherClass
    ) {
        $this->objectCollectionBatcherClass = $objectCollectionBatcherClass;
        $this->idCollectionBatcherClass = $idCollectionBatcherClass;
        $this->naiveIdRangeBatcherClass = $naiveIdRangeBatcherClass;
        $this->idRangeBatcherClass = $idRangeBatcherClass;
    }

    public function createObjectCollectionBatcher(
        QueryBuilder $qb,
        string $identifier = 'id',
        bool $clearOnBatch = true
    ): CollectionBatcherInterface {
        return new $this->objectCollectionBatcherClass($qb, $identifier, $clearOnBatch);
    }

    public function createIdCollectionBatcher(
        QueryBuilder $qb,
        string $identifier = 'id',
        bool $clearOnBatch = true
    ): CollectionBatcherInterface {
        return new $this->idCollectionBatcherClass($qb, $identifier, $clearOnBatch);
    }

    public function createIdRangeBatcher(
        QueryBuilder $qb,
        string $identifier = 'id',
        bool $clearOnBatch = true,
        int $sparsenessThreshold = 5
    ): RangeBatcherInterface {
        /** @var NaiveIdRangeBatcherInterface $naiveIdBatcher */
        $naiveIdBatcher = new $this->naiveIdRangeBatcherClass($qb, $identifier, $clearOnBatch);
        if ($naiveIdBatcher->getSparseness() <= $sparsenessThreshold) {
            return $naiveIdBatcher;
        }

        return new $this->idRangeBatcherClass($qb, $identifier, $clearOnBatch);
    }
}
