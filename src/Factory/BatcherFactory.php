<?php

declare(strict_types=1);

namespace Setono\DoctrineORMBatcher\Factory;

use Doctrine\ORM\QueryBuilder;
use Setono\DoctrineORMBatcher\Batcher\Collection\CollectionBatcherInterface;

final class BatcherFactory implements BatcherFactoryInterface
{
    private string $objectCollectionBatcherClass;

    private string $idCollectionBatcherClass;

    public function __construct(
        string $objectCollectionBatcherClass,
        string $idCollectionBatcherClass
    ) {
        $this->objectCollectionBatcherClass = $objectCollectionBatcherClass;
        $this->idCollectionBatcherClass = $idCollectionBatcherClass;
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
}
