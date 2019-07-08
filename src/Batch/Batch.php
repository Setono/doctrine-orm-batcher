<?php

declare(strict_types=1);

namespace Setono\DoctrineORMBatcher\Batch;

use Doctrine\ORM\QueryBuilder;

abstract class Batch implements BatchInterface
{
    /** @var string */
    protected $dql;

    /** @var array */
    protected $parameters;

    public function __construct(QueryBuilder $qb)
    {
        $this->dql = $qb->getDQL();
        $this->parameters = $qb->getParameters()->toArray();
    }

    public function getDql(): string
    {
        return $this->dql;
    }

    public function getParameters(): array
    {
        return $this->parameters;
    }
}
