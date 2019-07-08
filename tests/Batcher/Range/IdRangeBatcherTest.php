<?php

declare(strict_types=1);

namespace Tests\Setono\DoctrineORMBatcher\Batcher\Range;

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

        $expected = [
            ['lowerBound' => 10, 'upperBound' => 21],
            ['lowerBound' => 22, 'upperBound' => 37],
            ['lowerBound' => 38, 'upperBound' => 47],
            ['lowerBound' => 48, 'upperBound' => 84],
            ['lowerBound' => 85, 'upperBound' => 94],
            ['lowerBound' => 95, 'upperBound' => 100],
        ];

        $batches = $batcher->getBatches(10);

        foreach ($batches as $idx => $batch) {
            $this->assertSame($expected[$idx]['lowerBound'], $batch->getLowerBound());
            $this->assertSame($expected[$idx]['upperBound'], $batch->getUpperBound());
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

        $expected = [
            ['lowerBound' => 10, 'upperBound' => 21],
            ['lowerBound' => 22, 'upperBound' => 37],
            ['lowerBound' => 38, 'upperBound' => 47],
            ['lowerBound' => 48, 'upperBound' => 84],
            ['lowerBound' => 85, 'upperBound' => 94],
            ['lowerBound' => 95, 'upperBound' => 104],
            ['lowerBound' => 105, 'upperBound' => 105],
        ];

        $batches = $batcher->getBatches(10);

        foreach ($batches as $idx => $batch) {
            $this->assertSame($expected[$idx]['lowerBound'], $batch->getLowerBound());
            $this->assertSame($expected[$idx]['upperBound'], $batch->getUpperBound());
        }

        $this->assertSame(6, $idx);
    }
}
