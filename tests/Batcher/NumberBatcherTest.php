<?php

namespace Tests\Setono\DoctrineORMBatcher\Badger;

use PHPUnit\Framework\TestCase;
use Setono\DoctrineORMBatcher\Batch\Batch;
use Setono\DoctrineORMBatcher\Batcher\NumberBatcher;

final class NumberBatcherTest extends TestCase
{
    /**
     * @test
     */
    public function it_batches(): void
    {
        $batcher = new NumberBatcher();

        /** @var Batch[] $expected */
        $expected = [
            new Batch(12, 21),
            new Batch(22, 31),
            new Batch(32, 41),
            new Batch(42, 50),
        ];

        $batches = $batcher->getBatches(12, 50, 10);

        foreach ($batches as $idx => $batch) {
            $this->assertSame($expected[$idx]->getLowerBound(), $batch->getLowerBound());
            $this->assertSame($expected[$idx]->getUpperBound(), $batch->getUpperBound());
        }

        $this->assertSame(3, $idx);
    }
}
