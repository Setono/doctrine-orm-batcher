<?php

namespace Tests\Setono\DoctrineORMBatcher\Badger;

use PHPUnit\Framework\TestCase;
use Setono\DoctrineORMBatcher\Batcher\NumberBatcher;

final class NumberBatcherTest extends TestCase
{
    /**
     * @test
     */
    public function it_batches(): void
    {
        $batcher = new NumberBatcher();

        $expected = [
            [12, 21],
            [22, 31],
            [32, 41],
            [42, 50],
        ];

        $actual = iterator_to_array($batcher->getBatches(12, 50, 10));

        $this->assertSame($expected, $actual);
    }
}
