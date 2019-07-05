<?php

declare(strict_types=1);

namespace Setono\DoctrineORMBatcher\Batcher\Range;

use Safe\Exceptions\StringsException;
use Setono\DoctrineORMBatcher\Batch\RangeBatch;

final class NumberBatcher implements NumberBatcherInterface
{
    /**
     * @return iterable|RangeBatch[]
     *
     * @throws StringsException
     */
    public function getBatches(int $min, int $max, int $batchSize = 100): iterable
    {
        $batches = (int) ceil((($max - $min) + 1) / $batchSize);

        for ($batch = 0; $batch < $batches; ++$batch) {
            $lastBatch = ($batch + 1 === $batches);

            $firstNumber = $batch * $batchSize + $min;
            $lastNumber = $lastBatch ? $max : ($firstNumber + $batchSize) - 1;

            yield new RangeBatch($firstNumber, $lastNumber);
        }
    }
}
