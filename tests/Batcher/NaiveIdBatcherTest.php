<?php

declare(strict_types=1);

namespace Tests\Setono\DoctrineORMBatcher\Batcher;

use Doctrine\Common\Persistence\ManagerRegistry;
use Setono\DoctrineORMBatcher\Batch\Batch;
use Setono\DoctrineORMBatcher\Batcher\NaiveIdBatcher;
use Setono\DoctrineORMBatcher\Batcher\NumberBatcher;
use Tests\Setono\DoctrineORMBatcher\Entity\Entity;
use Tests\Setono\DoctrineORMBatcher\EntityManagerAwareTestCase;

final class NaiveIdBatcherTest extends EntityManagerAwareTestCase
{
    /**
     * @test
     */
    public function it_works(): void
    {
        $this->purger->purge();
        for ($i = 10; $i <= 52; ++$i) {
            $entity = new Entity($i);
            $this->entityManager->persist($entity);
        }

        $this->entityManager->flush();

        $batcher = $this->getBatcher();

        /** @var Batch[] $expected */
        $expected = [
            new Batch(10, 19),
            new Batch(20, 29),
            new Batch(30, 39),
            new Batch(40, 49),
            new Batch(50, 52),
        ];

        $batches = $batcher->getBatches(10);

        foreach ($batches as $idx => $batch) {
            $this->assertSame($expected[$idx]->getLowerBound(), $batch->getLowerBound());
            $this->assertSame($expected[$idx]->getUpperBound(), $batch->getUpperBound());
        }

        $this->assertSame(4, $idx);
    }

    /**
     * @test
     */
    public function it_is_not_sparse(): void
    {
        $this->purger->purge();

        for ($i = 10; $i <= 15; ++$i) {
            $entity = new Entity($i);
            $this->entityManager->persist($entity);
        }

        for ($i = 18; $i <= 52; ++$i) {
            $entity = new Entity($i);
            $this->entityManager->persist($entity);
        }

        $this->entityManager->flush();

        $batcher = $this->getBatcher();

        $sparseness = $batcher->getSparseness();

        $this->assertSame(5, $sparseness);
    }

    private function getBatcher(): NaiveIdBatcher
    {
        $managerRegistry = $this->createMock(ManagerRegistry::class);
        $managerRegistry->method('getManagerForClass')->willReturn($this->entityManager);

        return new NaiveIdBatcher($managerRegistry, Entity::class, new NumberBatcher());
    }
}
