<?php

declare(strict_types=1);

namespace Setono\DoctrineORMBatcher\Batch;

use Doctrine\ORM\QueryBuilder;

final class CollectionBatch extends Batch implements CollectionBatchInterface
{
    public const PARAMETER_COLLECTION = 'collection';

    private array $collection;

    public function __construct(array $collection, QueryBuilder $qb)
    {
        $this->collection = $collection;

        $qb->setParameter(self::PARAMETER_COLLECTION, $this->getCollection());

        /** @psalm-suppress ImpureMethodCall */
        parent::__construct($qb);
    }

    public function getCollection(): array
    {
        return $this->collection;
    }
}
