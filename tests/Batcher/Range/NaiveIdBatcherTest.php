<?php

declare(strict_types=1);

namespace Tests\Setono\DoctrineORMBatcher\Batcher\Range;

use Setono\DoctrineORMBatcher\Batch\RangeBatch;
use Setono\DoctrineORMBatcher\Batcher\Range\NaiveIdBatcher;
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

        /** @var RangeBatch[] $expected */
        $expected = [
            new RangeBatch(10, 19),
            new RangeBatch(20, 29),
            new RangeBatch(30, 39),
            new RangeBatch(40, 49),
            new RangeBatch(50, 52),
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

    private function getBatcher(): NaiveIdBatcher
    {
        $qb = $this->entityManager->createQueryBuilder();
        $qb->select('o')->from(Entity::class, 'o');

        return new NaiveIdBatcher($qb);
    }
}
