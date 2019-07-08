<?php

declare(strict_types=1);

namespace Setono\DoctrineORMBatcher\Batcher;

use Doctrine\ORM\NoResultException;
use Doctrine\ORM\QueryBuilder;
use Safe\Exceptions\StringsException;
use function Safe\sprintf;
use Symfony\Component\PropertyAccess\PropertyAccess;
use Symfony\Component\PropertyAccess\PropertyAccessorInterface;

abstract class Batcher implements BatcherInterface
{
    /** @var string */
    protected $identifier;

    /** @var string */
    protected $alias;

    /** @var QueryBuilder */
    private $qb;

    /** @var int */
    private $min;

    /** @var int */
    private $max;

    /** @var PropertyAccessorInterface */
    private $propertyAccessor;

    public function __construct(QueryBuilder $qb, string $identifier = 'id')
    {
        $this->qb = clone $qb;
        $this->identifier = $identifier;

        $rootAliases = $this->qb->getRootAliases();
        if (1 !== count($rootAliases)) {
            throw new \InvalidArgumentException('The query builder must have exactly one root alias'); // todo better exception
        }

        $this->alias = $rootAliases[0];

        $this->propertyAccessor = PropertyAccess::createPropertyAccessorBuilder()
            ->enableExceptionOnInvalidIndex()
            ->enableExceptionOnInvalidPropertyPath()
            ->getPropertyAccessor()
        ;
    }

    /**
     * Notice that the $select must include the identifier in some way.
     * If the $select is null the original select statement will be used.
     *
     * @throws StringsException
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

    /**
     * @throws NoResultException
     * @throws StringsException
     */
    protected function getMin(): int
    {
        if (null === $this->min) {
            $this->initMinMax();
        }

        return $this->min;
    }

    /**
     * @throws NoResultException
     * @throws StringsException
     */
    protected function getMax(): int
    {
        if (null === $this->max) {
            $this->initMinMax();
        }

        return $this->max;
    }

    /**
     * @throws NoResultException
     * @throws StringsException
     */
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
}
