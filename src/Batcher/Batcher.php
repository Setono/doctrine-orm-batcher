<?php

declare(strict_types=1);

namespace Setono\DoctrineORMBatcher\Batcher;

use Doctrine\ORM\NoResultException;
use Doctrine\ORM\QueryBuilder;
use InvalidArgumentException;
use Symfony\Component\PropertyAccess\PropertyAccess;
use Symfony\Component\PropertyAccess\PropertyAccessorInterface;
use Webmozart\Assert\Assert;

abstract class Batcher implements BatcherInterface
{
    private QueryBuilder $qb;

    protected string $identifier;

    private bool $clearOnBatch;

    protected string $alias;

    private ?int $min = null;

    private ?int $max = null;

    private ?int $count = null;

    private PropertyAccessorInterface $propertyAccessor;

    /**
     * @param QueryBuilder $qb           The query builder you have built for fetching objects
     * @param string       $identifier   The identifier of the root entity in your query builder
     * @param bool         $clearOnBatch If true it will clear the entity manager on each new batch
     */
    public function __construct(QueryBuilder $qb, string $identifier = 'id', bool $clearOnBatch = true)
    {
        $this->qb = clone $qb;
        $this->identifier = $identifier;
        $this->clearOnBatch = $clearOnBatch;

        $rootAliases = $this->qb->getRootAliases();
        if (1 !== count($rootAliases)) {
            throw new InvalidArgumentException('The query builder must have exactly one root alias'); // todo better exception
        }

        $this->alias = $rootAliases[0];

        $this->propertyAccessor = PropertyAccess::createPropertyAccessorBuilder()
            ->enableExceptionOnInvalidIndex()
            ->enableExceptionOnInvalidPropertyPath()
            ->getPropertyAccessor()
        ;
    }

    public function getBatchCount(int $batchSize = 100): int
    {
        return (int) ceil($this->getCount() / $batchSize);
    }

    /**
     * Notice that the $select must include the identifier in some way.
     * If the $select is null the original select statement will be used.
     */
    protected function getResult(string $select = null, int $batchSize = 100): iterable
    {
        $qb = $this->getQueryBuilder();

        if (null !== $select) {
            $qb->select($select);
        }

        $qb->orderBy(sprintf('%s.%s', $this->alias, $this->identifier), 'ASC')
            ->andWhere(sprintf('%s.%s > :lastId', $this->alias, $this->identifier))
            ->setMaxResults($batchSize)
        ;

        $lastId = 0;

        while (true) {
            $this->clear();

            $qb->setParameter('lastId', $lastId);
            $result = $qb->getQuery()->getResult();

            if (0 === count($result)) {
                break;
            }

            $lastRow = $result[count($result) - 1];

            $propertyPath = is_array($lastRow) ? sprintf('[%s]', $this->identifier) : $this->identifier;
            $lastId = $this->propertyAccessor->getValue($lastRow, $propertyPath);

            yield $result;
        }

        $this->clear();
    }

    private function clear(): void
    {
        if (!$this->clearOnBatch) {
            return;
        }

        $this->qb->getEntityManager()->clear();
    }

    /**
     * This is made to avoid side effects by passing around the query builder object.
     */
    protected function getQueryBuilder(): QueryBuilder
    {
        return clone $this->qb;
    }

    /**
     * This will return a query builder where the constraints for the respective batcher are added.
     */
    abstract protected function getBatchableQueryBuilder(): QueryBuilder;

    protected function getMin(): int
    {
        if (null === $this->min) {
            $this->initMinMax();
        }

        Assert::notNull($this->min);

        return $this->min;
    }

    protected function getMax(): int
    {
        if (null === $this->max) {
            $this->initMinMax();
        }

        Assert::notNull($this->max);

        return $this->max;
    }

    protected function getCount(): int
    {
        if (null === $this->count) {
            $this->initCount();
        }

        Assert::notNull($this->count);

        return $this->count;
    }

    private function initMinMax(): void
    {
        $qb = $this->getQueryBuilder();

        $qb->select(sprintf('MIN(%s.%s) as min, MAX(%s.%s) as max', $this->alias, $this->identifier, $this->alias, $this->identifier));

        $res = $qb->getQuery()->getScalarResult();
        if (count($res) < 1) {
            throw new NoResultException();
        }

        $row = $res[0];

        if (null === $row['min'] || null === $row['max']) {
            throw new NoResultException();
        }

        $this->min = (int) $row['min'];
        $this->max = (int) $row['max'];
    }

    private function initCount(): void
    {
        $qb = $this->getQueryBuilder();
        $qb->select(sprintf('COUNT(%s) as c', $this->alias));

        $this->count = (int) $qb->getQuery()->getSingleScalarResult();
    }
}
