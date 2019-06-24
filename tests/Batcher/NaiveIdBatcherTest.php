<?php

namespace Tests\Setono\DoctrineORMBatcher;

use Doctrine\Common\Persistence\ManagerRegistry;
use Setono\DoctrineORMBatcher\Batch\Batch;
use Setono\DoctrineORMBatcher\Batcher\NaiveIdBatcher;
use Setono\DoctrineORMBatcher\Batcher\NumberBatcher;
use Tests\Setono\DoctrineORMBatcher\Stub\Entity\ValidEntity;

final class NaiveIdBatcherTest extends EntityManagerAwareTestCase
{
    /**
     * @test
     */
    public function it_works(): void
    {
        $managerRegistry = $this->createMock(ManagerRegistry::class);
        $managerRegistry->method('getManagerForClass')->willReturn($this->entityManager);
        $idBatch = new NaiveIdBatcher($managerRegistry, ValidEntity::class, new NumberBatcher());

        /** @var Batch[] $expected */
        $expected = [
            new Batch(10, 19),
            new Batch(20, 29),
            new Batch(30, 39),
            new Batch(40, 49),
            new Batch(50, 52),
        ];

        $batches = $idBatch->getBatches(10);

        foreach ($batches as $idx => $batch) {
            $this->assertSame($expected[$idx]->getLowerBound(), $batch->getLowerBound());
            $this->assertSame($expected[$idx]->getUpperBound(), $batch->getUpperBound());
        }

        $this->assertSame(4, $idx);
    }
}
