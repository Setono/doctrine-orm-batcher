<?php

declare(strict_types=1);

namespace Setono\DoctrineORMBatcher\Batcher;

use RuntimeException;
use Safe\Exceptions\StringsException;
use function Safe\sprintf;
use Setono\DoctrineORMBatcher\Batch\Batch;

final class BestChoiceIdBatcher implements IdBatcherInterface
{
    /** @var NaiveIdBatcherInterface */
    private $naiveIdBatcher;

    /** @var RealIdBatcherInterface */
    private $realIdBatcher;

    /** @var int */
    private $sparsenessThreshold;

    public function __construct(NaiveIdBatcherInterface $naiveIdBatcher, RealIdBatcherInterface $realIdBatcher, int $sparsenessThreshold = 10)
    {
        $this->naiveIdBatcher = $naiveIdBatcher;
        $this->realIdBatcher = $realIdBatcher;
        $this->sparsenessThreshold = $sparsenessThreshold;
    }

    /**
     * @return iterable|Batch[]
     */
    public function getBatches(int $batchSize = 100): iterable
    {
        if ($this->naiveIdBatcher->getSparseness() <= $this->sparsenessThreshold) {
            yield from $this->naiveIdBatcher->getBatches($batchSize);
        } else {
            yield from $this->realIdBatcher->getBatches($batchSize);
        }
    }

    /**
     * @throws StringsException
     */
    public function getSparseness(): int
    {
        throw new RuntimeException(sprintf('No sparseness available on the %s', self::class)); // better exception
    }
}
