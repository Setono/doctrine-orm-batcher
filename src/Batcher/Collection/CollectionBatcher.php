<?php

declare(strict_types=1);

namespace Setono\DoctrineORMBatcher\Batcher\Collection;

use Doctrine\ORM\QueryBuilder;
use Setono\DoctrineORMBatcher\Batch\CollectionBatch;
use Setono\DoctrineORMBatcher\Batcher\Batcher;

abstract class CollectionBatcher extends Batcher implements CollectionBatcherInterface
{
    protected function getBatchableQueryBuilder(): QueryBuilder
    {
        $qb = $this->getQueryBuilder();
        $qb->andWhere(sprintf('%s.%s IN(:%s)', $this->alias, $this->identifier, CollectionBatch::PARAMETER_COLLECTION));

        return $qb;
    }
}
