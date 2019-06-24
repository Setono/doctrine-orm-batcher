<?php

namespace Setono\DoctrineORMBatcher\Batcher;

final class NumberBatcher implements NumberBatcherInterface
{
    public function getBatches(int $min, int $max, int $batchSize = 100): iterable
    {
        $batches = (int) ceil((($max - $min) + 1) / $batchSize);

        for($batch = 0; $batch < $batches; $batch++) {
            $lastBatch = ($batch + 1 === $batches);

            $firstNumber = $batch * $batchSize + $min;
            $lastNumber = $lastBatch ? $max : ($firstNumber + $batchSize) - 1;

            yield [$firstNumber, $lastNumber];
        }
    }
}
