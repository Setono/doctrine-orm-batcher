<?php

declare(strict_types=1);

namespace Tests\Setono\DoctrineORMBatcher\Factory;

use Doctrine\ORM\QueryBuilder;
use Setono\DoctrineORMBatcher\Batcher\Range\NaiveIdRangeBatcherInterface;
use Setono\DoctrineORMBatcher\Batcher\Range\RangeBatcherInterface;
use Setono\DoctrineORMBatcher\Factory\BatcherFactory;
use Tests\Setono\DoctrineORMBatcher\Entity\Entity;
use Tests\Setono\DoctrineORMBatcher\EntityManagerAwareTestCase;

final class BatcherFactoryTest extends EntityManagerAwareTestCase
{
    /**
     * @test
     */
    public function will_use_naive_id_batcher(): void
    {
        $qb = $this->createQb();

        $cls = new class() implements NaiveIdRangeBatcherInterface {
            public function getSparseness(): int
            {
                return 1;
            }

            public function getBatches(int $batchSize = 100): iterable
            {
                return [];
            }

            public function getBatchCount(int $batchSize = 100): int
            {
                return 0;
            }
        };

        $factory = new BatcherFactory('', '', get_class($cls), '');

        $batcher = $factory->createIdRangeBatcher($qb);

        $this->assertInstanceOf(NaiveIdRangeBatcherInterface::class, $batcher);
    }

    /**
     * @test
     */
    public function will_use_id_batcher(): void
    {
        $qb = $this->createQb();

        $naiveIdBatcher = new class() implements NaiveIdRangeBatcherInterface {
            public function getSparseness(): int
            {
                return 6;
            }

            public function getBatches(int $batchSize = 100): iterable
            {
                return [];
            }

            public function getBatchCount(int $batchSize = 100): int
            {
                return 0;
            }
        };

        $idBatcher = new class() implements RangeBatcherInterface {
            public function getSparseness(): int
            {
                return 6;
            }

            public function getBatches(int $batchSize = 100): iterable
            {
                return [];
            }

            public function getBatchCount(int $batchSize = 100): int
            {
                return 0;
            }
        };

        $factory = new BatcherFactory('', '', get_class($naiveIdBatcher), get_class($idBatcher));

        $batcher = $factory->createIdRangeBatcher($qb);

        $this->assertNotInstanceOf(NaiveIdRangeBatcherInterface::class, $batcher);
        $this->assertInstanceOf(RangeBatcherInterface::class, $batcher);
    }

    private function createQb(): QueryBuilder
    {
        $qb = $this->entityManager->createQueryBuilder();
        $qb->select('o')->from(Entity::class, 'o');

        return $qb;
    }
}
