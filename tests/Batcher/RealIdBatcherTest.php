<?php

declare(strict_types=1);

namespace Tests\Setono\DoctrineORMBatcher\Batcher;

use Doctrine\Common\Persistence\ManagerRegistry;
use Setono\DoctrineORMBatcher\Batch\Batch;
use Setono\DoctrineORMBatcher\Batcher\RealIdBatcher;
use Tests\Setono\DoctrineORMBatcher\Entity\Entity;
use Tests\Setono\DoctrineORMBatcher\EntityManagerAwareTestCase;

final class RealIdBatcherTest extends EntityManagerAwareTestCase
{
    /**
     * @test
     */
    public function it_works(): void
    {
        $this->purger->purge();

        for ($i = 10; $i <= 15; ++$i) {
            $entity = new Entity($i);
            $this->entityManager->persist($entity);
        }

        for ($i = 18; $i <= 28; ++$i) {
            $entity = new Entity($i);
            $this->entityManager->persist($entity);
        }

        for ($i = 35; $i <= 50; ++$i) {
            $entity = new Entity($i);
            $this->entityManager->persist($entity);
        }

        for ($i = 78; $i <= 100; ++$i) {
            $entity = new Entity($i);
            $this->entityManager->persist($entity);
        }

        $this->entityManager->flush();

        $managerRegistry = $this->createMock(ManagerRegistry::class);
        $managerRegistry->method('getManagerForClass')->willReturn($this->entityManager);
        $idBatch = new RealIdBatcher($managerRegistry, Entity::class);

        /** @var Batch[] $expected */
        $expected = [
            new Batch(10, 21),
            new Batch(22, 37),
            new Batch(38, 47),
            new Batch(48, 84),
            new Batch(85, 94),
            new Batch(95, 100),
        ];

        $batches = $idBatch->getBatches(10);

        foreach ($batches as $idx => $batch) {
            $this->assertSame($expected[$idx]->getLowerBound(), $batch->getLowerBound());
            $this->assertSame($expected[$idx]->getUpperBound(), $batch->getUpperBound());
        }

        $this->assertSame(5, $idx);
    }

    /**
     * @test
     */
    public function it_works_too(): void
    {
        $this->purger->purge();

        for ($i = 10; $i <= 15; ++$i) {
            $entity = new Entity($i);
            $this->entityManager->persist($entity);
        }

        for ($i = 18; $i <= 28; ++$i) {
            $entity = new Entity($i);
            $this->entityManager->persist($entity);
        }

        for ($i = 35; $i <= 50; ++$i) {
            $entity = new Entity($i);
            $this->entityManager->persist($entity);
        }

        for ($i = 78; $i <= 105; ++$i) {
            $entity = new Entity($i);
            $this->entityManager->persist($entity);
        }

        $this->entityManager->flush();

        $managerRegistry = $this->createMock(ManagerRegistry::class);
        $managerRegistry->method('getManagerForClass')->willReturn($this->entityManager);
        $idBatch = new RealIdBatcher($managerRegistry, Entity::class);

        /** @var Batch[] $expected */
        $expected = [
            new Batch(10, 21),
            new Batch(22, 37),
            new Batch(38, 47),
            new Batch(48, 84),
            new Batch(85, 94),
            new Batch(95, 104),
            new Batch(105, 105),
        ];

        $batches = $idBatch->getBatches(10);

        foreach ($batches as $idx => $batch) {
            $this->assertSame($expected[$idx]->getLowerBound(), $batch->getLowerBound());
            $this->assertSame($expected[$idx]->getUpperBound(), $batch->getUpperBound());
        }

        $this->assertSame(6, $idx);
    }
}
