<?php

declare(strict_types=1);

namespace Setono\DoctrineORMBatcher\Repository;

use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\Mapping\ClassMetadata;
use Doctrine\ORM\Mapping\MappingException;
use Doctrine\ORM\QueryBuilder;
use Safe\Exceptions\StringsException;
use function Safe\sprintf;
use Setono\DoctrineORMBatcher\Batch\Batch;

/**
 * If you use the @see EntityRepository you can import this trait to implement the BatchableInterface.
 */
trait BatchableTrait
{
    /** @var ClassMetadata */
    protected $_class;

    /**
     * @return QueryBuilder
     */
    abstract public function createQueryBuilder(string $alias, ?string $indexBy = null);

    /**
     * Notice that this trait presumes you have a single identifier (not composite) and it is an integer.
     *
     * @return object[]
     *
     * @throws MappingException
     * @throws StringsException
     */
    public function getBatch(Batch $batch, callable $queryBuilderUpdater = null): array
    {
        $identifier = $this->_class->getSingleIdentifierFieldName();
        $alias = 'o';

        $qb = $this->createQueryBuilder($alias);
        $qb
            ->andWhere(sprintf('%s.%s >= :lowerBound', $alias, $identifier))
            ->andWhere(sprintf('%s.%s <= :upperBound', $alias, $identifier))
            ->setParameters([
                'lowerBound' => $batch->getLowerBound(),
                'upperBound' => $batch->getUpperBound(),
            ])
        ;

        if (null !== $queryBuilderUpdater) {
            $queryBuilderUpdater($qb, $alias);
        }

        return $qb->getQuery()->getResult();
    }
}
