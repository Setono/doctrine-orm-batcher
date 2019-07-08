<?php

declare(strict_types=1);

namespace Setono\DoctrineORMBatcher\Batcher\Collection;

use Doctrine\ORM\QueryBuilder;
use Safe\Exceptions\StringsException;
use function Safe\sprintf;
use Setono\DoctrineORMBatcher\Batch\CollectionBatch;
use Setono\DoctrineORMBatcher\Batcher\Batcher;

abstract class CollectionBatcher extends Batcher
{
    /**
     * @throws StringsException
     */
    protected function getBatchableQueryBuilder(): QueryBuilder
    {
        $qb = $this->getQueryBuilder();
        $qb->andWhere(sprintf('%s.%s IN(:%s)', $this->alias, $this->identifier, CollectionBatch::PARAMETER_COLLECTION));

        return $qb;
    }
}
