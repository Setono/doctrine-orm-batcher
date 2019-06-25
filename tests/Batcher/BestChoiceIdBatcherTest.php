<?php

namespace Tests\Setono\DoctrineORMBatcher;

use Doctrine\Common\Persistence\ManagerRegistry;
use Setono\DoctrineORMBatcher\Batch\Batch;
use Setono\DoctrineORMBatcher\Batcher\BestChoiceIdBatcher;
use Setono\DoctrineORMBatcher\Batcher\NaiveIdBatcher;
use Setono\DoctrineORMBatcher\Batcher\NaiveIdBatcherInterface;
use Setono\DoctrineORMBatcher\Batcher\NumberBatcher;
use Setono\DoctrineORMBatcher\Batcher\RealIdBatcher;
use Setono\DoctrineORMBatcher\Batcher\RealIdBatcherInterface;
use Tests\Setono\DoctrineORMBatcher\Stub\Entity\ValidEntity;

final class BestChoiceIdBatcherTest extends EntityManagerAwareTestCase
{
    /**
     * @test
     */
    public function will_use_naive_id_batcher(): void
    {
        $managerRegistry = $this->createMock(ManagerRegistry::class);
        $managerRegistry->method('getManagerForClass')->willReturn($this->entityManager);

        $naiveIdBatcher = $this->createMock(NaiveIdBatcherInterface::class);
        $naiveIdBatcher->method('getSparseness')->willReturn(5);
        $naiveIdBatcher->method('getBatches')->willReturn(['BATCH']);
        $realIdBatcher = $this->createMock(RealIdBatcherInterface::class);

        $bestChoiceIdBatcher = new BestChoiceIdBatcher($naiveIdBatcher, $realIdBatcher);
        $batches = iterator_to_array($bestChoiceIdBatcher->getBatches());
        $this->assertSame('BATCH', $batches[0]);
    }

    /**
     * @test
     */
    public function will_use_real_id_batcher(): void
    {
        $managerRegistry = $this->createMock(ManagerRegistry::class);
        $managerRegistry->method('getManagerForClass')->willReturn($this->entityManager);

        $naiveIdBatcher = $this->createMock(NaiveIdBatcherInterface::class);
        $naiveIdBatcher->method('getSparseness')->willReturn(15);
        $realIdBatcher = $this->createMock(RealIdBatcherInterface::class);
        $realIdBatcher->method('getBatches')->willReturn(['BATCH']);

        $bestChoiceIdBatcher = new BestChoiceIdBatcher($naiveIdBatcher, $realIdBatcher);
        $batches = iterator_to_array($bestChoiceIdBatcher->getBatches());
        $this->assertSame('BATCH', $batches[0]);
    }
}
