<?php

declare(strict_types=1);

namespace Setono\DoctrineORMBatcher\Batch;

use Doctrine\ORM\QueryBuilder;
use InvalidArgumentException;

abstract class Batch implements BatchInterface
{
    protected string $class;

    protected string $dql;

    protected array $parameters;

    public function __construct(QueryBuilder $qb)
    {
        $rootEntities = $qb->getRootEntities();
        if (0 === count($rootEntities)) {
            throw new InvalidArgumentException('The number of root entities on the query builder must be one or more');
        }

        $this->class = $rootEntities[0];
        $this->dql = $qb->getDQL();
        $this->parameters = $qb->getParameters()->toArray();
    }

    public function getClass(): string
    {
        return $this->class;
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
