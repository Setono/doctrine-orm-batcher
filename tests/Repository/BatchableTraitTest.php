<?php

declare(strict_types=1);

namespace Tests\Setono\DoctrineORMBatcher\Repository;

use Doctrine\ORM\Repository\DefaultRepositoryFactory;
use Setono\DoctrineORMBatcher\Batch\Batch;
use Setono\DoctrineORMBatcher\Repository\BatchableInterface;
use Tests\Setono\DoctrineORMBatcher\Entity\Entity;
use Tests\Setono\DoctrineORMBatcher\EntityManagerAwareTestCase;

final class BatchableTraitTest extends EntityManagerAwareTestCase
{
    /**
     * @test
     */
    public function it_returns_batch(): void
    {
        // add entities
        $this->purger->purge();
        for ($i = 1; $i <= 30; ++$i) {
            $entity = new Entity($i);
            $this->entityManager->persist($entity);
        }

        $this->entityManager->flush();

        // test repository method
        $factory = new DefaultRepositoryFactory();

        /** @var BatchableInterface $repository */
        $repository = $factory->getRepository($this->entityManager, Entity::class);

        /** @var Entity[] $entities */
        $entities = $repository->getBatch(new Batch(10, 19));

        $this->assertCount(10, $entities);

        for ($i = 0; $i < 10; ++$i) {
            $entity = $entities[$i];

            $this->assertInstanceOf(Entity::class, $entity);

            $this->assertSame($i + 10, $entity->getId());
        }
    }
}
