<?php

declare(strict_types=1);

namespace Tests\Setono\DoctrineORMBatcher\Batcher\Range;

use PHPUnit\Framework\TestCase;
use Setono\DoctrineORMBatcher\Batch\RangeBatch;
use Setono\DoctrineORMBatcher\Batcher\Range\NumberBatcher;

final class NumberBatcherTest extends TestCase
{
    /**
     * @test
     */
    public function it_batches(): void
    {
        $batcher = new NumberBatcher();

        /** @var RangeBatch[] $expected */
        $expected = [
            new RangeBatch(12, 21),
            new RangeBatch(22, 31),
            new RangeBatch(32, 41),
            new RangeBatch(42, 50),
        ];

        $batches = $batcher->getBatches(12, 50, 10);

        foreach ($batches as $idx => $batch) {
            $this->assertSame($expected[$idx]->getLowerBound(), $batch->getLowerBound());
            $this->assertSame($expected[$idx]->getUpperBound(), $batch->getUpperBound());
        }

        $this->assertSame(3, $idx);
    }
}
