<?php

declare(strict_types=1);

namespace Tests\Setono\DoctrineORMBatcher\Batcher\Range;

use Setono\DoctrineORMBatcher\Batch\RangeBatch;
use Setono\DoctrineORMBatcher\Batcher\Range\IdRangeBatcher;
use Tests\Setono\DoctrineORMBatcher\Entity\Entity;
use Tests\Setono\DoctrineORMBatcher\EntityManagerAwareTestCase;

final class IdRangeBatcherTest extends EntityManagerAwareTestCase
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

        $qb = $this->entityManager->createQueryBuilder();
        $qb->select('o')->from(Entity::class, 'o');

        $batcher = new IdRangeBatcher($qb);

        /** @var RangeBatch[] $expected */
        $expected = [
            new RangeBatch(10, 21),
            new RangeBatch(22, 37),
            new RangeBatch(38, 47),
            new RangeBatch(48, 84),
            new RangeBatch(85, 94),
            new RangeBatch(95, 100),
        ];

        $batches = $batcher->getBatches(10);

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

        $qb = $this->entityManager->createQueryBuilder();
        $qb->select('o')->from(Entity::class, 'o');

        $batcher = new IdRangeBatcher($qb);

        /** @var RangeBatch[] $expected */
        $expected = [
            new RangeBatch(10, 21),
            new RangeBatch(22, 37),
            new RangeBatch(38, 47),
            new RangeBatch(48, 84),
            new RangeBatch(85, 94),
            new RangeBatch(95, 104),
            new RangeBatch(105, 105),
        ];

        $batches = $batcher->getBatches(10);

        foreach ($batches as $idx => $batch) {
            $this->assertSame($expected[$idx]->getLowerBound(), $batch->getLowerBound());
            $this->assertSame($expected[$idx]->getUpperBound(), $batch->getUpperBound());
        }

        $this->assertSame(6, $idx);
    }
}
