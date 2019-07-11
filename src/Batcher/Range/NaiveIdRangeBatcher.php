<?php

declare(strict_types=1);

namespace Setono\DoctrineORMBatcher\Batcher\Range;

use Doctrine\ORM\NonUniqueResultException;
use Doctrine\ORM\NoResultException;
use Safe\Exceptions\StringsException;
use function Safe\sprintf;
use Setono\DoctrineORMBatcher\Batch\RangeBatch;
use Setono\DoctrineORMBatcher\Batch\RangeBatchInterface;

final class NaiveIdRangeBatcher extends RangeBatcher implements NaiveIdRangeBatcherInterface
{
    /** @var int */
    private $count;

    /**
     * @return iterable<RangeBatchInterface>
     *
     * @throws NoResultException
     * @throws StringsException
     */
    public function getBatches(int $batchSize = 100): iterable
    {
        $min = $this->getMin();
        $max = $this->getMax();

        $batches = (int) ceil((($max - $min) + 1) / $batchSize);

        for ($batch = 0; $batch < $batches; ++$batch) {
            $lastBatch = ($batch + 1 === $batches);

            $firstNumber = $batch * $batchSize + $min;
            $lastNumber = $lastBatch ? $max : ($firstNumber + $batchSize) - 1;

            yield new RangeBatch($firstNumber, $lastNumber, $this->getBatchableQueryBuilder());
        }
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
