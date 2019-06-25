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
    public function getBatch(Batch $batch): array
    {
        $identifier = $this->_class->getSingleIdentifierFieldName();

        $qb = $this->createQueryBuilder('o');
        $qb
            ->andWhere(sprintf('o.%s >= :lowerBound', $identifier))
            ->andWhere(sprintf('o.%s <= :upperBound', $identifier))
            ->setParameters([
                'lowerBound' => $batch->getLowerBound(),
                'upperBound' => $batch->getUpperBound(),
            ])
        ;

        return $qb->getQuery()->getResult();
    }
}
