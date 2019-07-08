<?php

declare(strict_types=1);

namespace Tests\Setono\DoctrineORMBatcher\Batcher\Range;

use Doctrine\Common\Persistence\ManagerRegistry;
use Setono\DoctrineORMBatcher\Batcher\Range\BestIdRangeBatcher;
use Setono\DoctrineORMBatcher\Batcher\Range\IdRangeBatcherInterface;
use Setono\DoctrineORMBatcher\Batcher\Range\NaiveIdRangeBatcherInterface;
use Tests\Setono\DoctrineORMBatcher\Entity\Entity;
use Tests\Setono\DoctrineORMBatcher\EntityManagerAwareTestCase;

final class BestIdRangeBatcherTest extends EntityManagerAwareTestCase
{
    /**
     * @test
     */
    public function will_use_naive_id_batcher(): void
    {
        $qb = $this->entityManager->createQueryBuilder();
        $qb->select('o')->from(Entity::class, 'o');

        $naiveIdBatcher = $this->createMock(NaiveIdRangeBatcherInterface::class);
        $naiveIdBatcher->method('getSparseness')->willReturn(5);
        $naiveIdBatcher->method('getBatches')->willReturn(['BATCH']);
        $realIdBatcher = $this->createMock(IdRangeBatcherInterface::class);

        $bestChoiceIdBatcher = new BestIdRangeBatcher($naiveIdBatcher, $realIdBatcher);
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

        $naiveIdBatcher = $this->createMock(NaiveIdRangeBatcherInterface::class);
        $naiveIdBatcher->method('getSparseness')->willReturn(15);
        $realIdBatcher = $this->createMock(IdRangeBatcherInterface::class);
        $realIdBatcher->method('getBatches')->willReturn(['BATCH']);

        $bestChoiceIdBatcher = new BestIdRangeBatcher($naiveIdBatcher, $realIdBatcher);
        $batches = iterator_to_array($bestChoiceIdBatcher->getBatches());
        $this->assertSame('BATCH', $batches[0]);
    }
}
