<?php

declare(strict_types=1);

namespace Tests\Setono\DoctrineORMBatcher\Batcher\Collection;

use Setono\DoctrineORMBatcher\Batcher\Collection\ObjectCollectionBatcher;
use Tests\Setono\DoctrineORMBatcher\Entity\Entity;
use Tests\Setono\DoctrineORMBatcher\EntityManagerAwareTestCase;

final class ObjectCollectionBatcherTest extends EntityManagerAwareTestCase
{
    /**
     * @test
     */
    public function it_works(): void
    {
        $this->purger->purge();

        $expectedIds = [];

        for ($i = 10; $i <= 15; ++$i) {
            $entity = new Entity($i);
            $this->entityManager->persist($entity);

            $expectedIds[$i] = false;
        }

        for ($i = 18; $i <= 28; ++$i) {
            $entity = new Entity($i);
            $this->entityManager->persist($entity);

            $expectedIds[$i] = false;
        }

        for ($i = 35; $i <= 50; ++$i) {
            $entity = new Entity($i, false); // these entities are not expected in the resulting batches
            $this->entityManager->persist($entity);
        }

        for ($i = 78; $i <= 100; ++$i) {
            $entity = new Entity($i);
            $this->entityManager->persist($entity);

            $expectedIds[$i] = false;
        }

        $this->entityManager->flush();

        $qb = $this->entityManager->createQueryBuilder();
        $qb->select('o')
            ->from(Entity::class, 'o')
            ->andWhere('o.enabled = 1')
        ;

        $batcher = new ObjectCollectionBatcher($qb);

        $batches = $batcher->getBatches(10);

        foreach ($batches as $idx => $batch) {
            /** @var Entity $entity */
            foreach ($batch->getCollection() as $entity) {
                $this->assertArrayHasKey($entity->getId(), $expectedIds);

                $expectedIds[$entity->getId()] = true;
            }
        }

        // the call effectively removes all ids that are set to true (which means they were found)
        $idsNotFound = array_filter($expectedIds, static function ($expectedId) {
            return !$expectedId;
        });

        $this->assertCount(0, $idsNotFound);
    }
}
