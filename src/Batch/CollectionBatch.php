<?php

declare(strict_types=1);

namespace Setono\DoctrineORMBatcher\Batch;

use Doctrine\ORM\QueryBuilder;

final class CollectionBatch extends Batch
{
    public const PARAMETER_COLLECTION = 'collection';

    /** @var array */
    private $collection;

    public function __construct(array $collection, QueryBuilder $qb)
    {
        $this->collection = $collection;

        $qb->setParameters([
            self::PARAMETER_COLLECTION => $this->getCollection(),
        ]);

        parent::__construct($qb);
    }

    public function getCollection(): array
    {
        return $this->collection;
    }
}
