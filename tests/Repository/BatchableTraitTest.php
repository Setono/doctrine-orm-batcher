<?php

declare(strict_types=1);

namespace Tests\Setono\DoctrineORMBatcher\Repository;

use Doctrine\ORM\QueryBuilder;
use Doctrine\ORM\Repository\DefaultRepositoryFactory;
use function Safe\sprintf;
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

    /**
     * @test
     */
    public function it_updates_query_builder(): void
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
        $entities = $repository->getBatch(new Batch(10, 19), static function (QueryBuilder $queryBuilder, string $alias) {
            $queryBuilder->andWhere(sprintf('%s.id > 15', $alias));
        });

        $this->assertCount(4, $entities);

        for ($i = 0; $i < 4; ++$i) {
            $entity = $entities[$i];

            $this->assertInstanceOf(Entity::class, $entity);

            $this->assertSame($i + 16, $entity->getId());
        }
    }
}
