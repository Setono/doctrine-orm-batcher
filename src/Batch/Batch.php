<?php

declare(strict_types=1);

namespace Setono\DoctrineORMBatcher\Batch;

use Doctrine\ORM\Query\Parameter;
use Doctrine\ORM\QueryBuilder;
use InvalidArgumentException;

class Batch implements BatchInterface
{
    private array $data;

    /** @var class-string */
    protected string $class;

    protected string $dql;

    /**
     * @var array<int, Parameter>
     */
    protected array $parameters;

    public function __construct(array $data, QueryBuilder $qb)
    {
        /** @var array<array-key, class-string> $rootEntities */
        $rootEntities = $qb->getRootEntities();
        if (0 === count($rootEntities)) {
            throw new InvalidArgumentException('The number of root entities on the query builder must be one or more');
        }

        $this->data = $data;
        $this->class = $rootEntities[0];
        $this->dql = $qb->getDQL();
        $this->parameters = $qb->getParameters()->toArray();
    }

    public function getData(): array
    {
        return $this->data;
    }

    /**
     * @return class-string
     */
    public function getClass(): string
    {
        return $this->class;
    }

    public function getDql(): string
    {
        return $this->dql;
    }

    /**
     * @return array<int, Parameter>
     */
    public function getParameters(): array
    {
        return $this->parameters;
    }
}
