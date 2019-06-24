<?php

namespace Tests\Setono\DoctrineORMBatch;

use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Tools\SchemaTool;
use Doctrine\ORM\Tools\Setup;
use PHPUnit\Framework\TestCase;
use Tests\Setono\DoctrineORMBatch\Stub\Entity\ValidEntity;

abstract class EntityManagerAwareTestCase extends TestCase
{
    /** @var EntityManagerInterface */
    protected $entityManager;

    public function setUp(): void
    {
        parent::setUp();

        $config = Setup::createAnnotationMetadataConfiguration([__DIR__.'/Stub/Entity'], true);

        $this->entityManager = EntityManager::create([
            'driver' => 'pdo_sqlite',
            'path' => __DIR__ . '/db.sqlite',
        ], $config);

        $metadata = $this->entityManager->getMetadataFactory()->getAllMetadata();

        $schemaTool = new SchemaTool($this->entityManager);
        $schemaTool->dropDatabase();
        $schemaTool->updateSchema($metadata);

        for($i = 10; $i < 490; $i++) {
            $entity = new ValidEntity($i);
            $this->entityManager->persist($entity);
        }

        $this->entityManager->flush();

    }
}
