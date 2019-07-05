<?php

declare(strict_types=1);

namespace Setono\DoctrineORMBatcher\Batcher\Range;

use Doctrine\ORM\NonUniqueResultException;
use Doctrine\ORM\NoResultException;
use Doctrine\ORM\QueryBuilder;
use Safe\Exceptions\StringsException;
use function Safe\sprintf;
use Setono\DoctrineORMBatcher\Batch\RangeBatch;
use Setono\DoctrineORMBatcher\Batcher\Batcher;

final class NaiveIdBatcher extends Batcher implements NaiveIdBatcherInterface
{
    /** @var NumberBatcherInterface */
    private $numberBatcher;

    /** @var int */
    private $count;

    public function __construct(QueryBuilder $qb, string $identifier = 'id', NumberBatcherInterface $numberBatcher = null)
    {
        parent::__construct($qb, $identifier);

        if (null === $numberBatcher) {
            $numberBatcher = new NumberBatcher();
        }

        $this->numberBatcher = $numberBatcher;
    }

    /**
     * @return iterable|RangeBatch[]
     *
     * @throws NoResultException
     * @throws StringsException
     */
    public function getBatches(int $batchSize = 100): iterable
    {
        yield from $this->numberBatcher->getBatches($this->getMin(), $this->getMax(), $batchSize);
    }

    /**
     * If the lowest id is 30 and the highest id is 190 the maximum number of rows is (190 - 30) + 1 = 161
     * If the number of rows is 145, then the sparseness is (161 - 145) / 161 * 100 = 9.94% and this method will return 10 in that case.
     *
     * @throws NoResultException
     * @throws NonUniqueResultException
     * @throws StringsException
     */
    public function getSparseness(): int
    {
        if (null === $this->count) {
            $this->initCount();
        }

        $bestPossibleCount = ($this->getMax() - $this->getMin()) + 1;

        return (int) round(($bestPossibleCount - $this->count) / $bestPossibleCount * 100);
    }

    /**
     * @throws NonUniqueResultException
     * @throws StringsException
     */
    private function initCount(): void
    {
        $qb = $this->getQueryBuilder();
        $qb->select(sprintf('COUNT(%s) as c', $this->alias));

        $this->count = (int) $qb->getQuery()->getSingleScalarResult();
    }
}
