<?php

declare(strict_types=1);

namespace Setono\DoctrineORMBatcher\Batcher\Range;

use Setono\DoctrineORMBatcher\Batch\RangeBatch;
use Setono\DoctrineORMBatcher\Batcher\BatcherInterface;

final class BestIdRangeBatcher implements BatcherInterface
{
    /** @var BatcherInterface */
    private $bestChoiceIdBatcher;

    /** @var NaiveIdRangeBatcherInterface */
    private $naiveIdBatcher;

    /** @var IdRangeBatcherInterface */
    private $realIdBatcher;

    /** @var int */
    private $sparsenessThreshold;

    public function __construct(NaiveIdRangeBatcherInterface $naiveIdBatcher, IdRangeBatcherInterface $realIdBatcher, int $sparsenessThreshold = 5)
    {
        $this->naiveIdBatcher = $naiveIdBatcher;
        $this->realIdBatcher = $realIdBatcher;
        $this->sparsenessThreshold = $sparsenessThreshold;
    }

    /**
     * @return iterable|RangeBatch[]
     */
    public function getBatches(int $batchSize = 100): iterable
    {
        yield from $this->getBestChoiceIdBatcher()->getBatches($batchSize);
    }

    private function getBestChoiceIdBatcher(): BatcherInterface
    {
        if (null === $this->bestChoiceIdBatcher) {
            if ($this->naiveIdBatcher->getSparseness() <= $this->sparsenessThreshold) {
                $this->bestChoiceIdBatcher = $this->naiveIdBatcher;
            } else {
                $this->bestChoiceIdBatcher = $this->realIdBatcher;
            }
        }

        return $this->bestChoiceIdBatcher;
    }
}
