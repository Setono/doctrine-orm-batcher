<?php

declare(strict_types=1);

namespace Tests\Setono\DoctrineORMBatcher\Batcher\Range;

use Setono\DoctrineORMBatcher\Batcher\Range\NaiveIdRangeBatcher;
use Tests\Setono\DoctrineORMBatcher\Entity\Entity;
use Tests\Setono\DoctrineORMBatcher\EntityManagerAwareTestCase;

final class NaiveIdRangeBatcherTest extends EntityManagerAwareTestCase
{
    private const BATCH_SIZE = 10;

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

        $this->assertSame(5, $batcher->getBatchCount(self::BATCH_SIZE));

        $expected = [
            ['lowerBound' => 10, 'upperBound' => 19],
            ['lowerBound' => 20, 'upperBound' => 29],
            ['lowerBound' => 30, 'upperBound' => 39],
            ['lowerBound' => 40, 'upperBound' => 49],
            ['lowerBound' => 50, 'upperBound' => 52],
        ];

        $batches = $batcher->getBatches(self::BATCH_SIZE);

        foreach ($batches as $idx => $batch) {
            $this->assertSame($expected[$idx]['lowerBound'], $batch->getLowerBound());
            $this->assertSame($expected[$idx]['upperBound'], $batch->getUpperBound());
        }

        $this->assertSame(4, $idx);
    }

    /**
     * @test
     */
    public function it_is_not_sparse(): void
    {
        $this->purger->purge();

        // 6 rows
        for ($i = 10; $i <= 15; ++$i) {
            $entity = new Entity($i);
            $this->entityManager->persist($entity);
        }

        // 35 rows
        for ($i = 18; $i <= 52; ++$i) {
            $entity = new Entity($i);
            $this->entityManager->persist($entity);
        }

        $this->entityManager->flush();

        $batcher = $this->getBatcher();

        $sparseness = $batcher->getSparseness();

        $this->assertSame(5, $sparseness);
    }

    private function getBatcher(): NaiveIdRangeBatcher
    {
        $qb = $this->entityManager->createQueryBuilder();
        $qb->select('o')->from(Entity::class, 'o');

        return new NaiveIdRangeBatcher($qb);
    }
}
