<?php

declare(strict_types=1);

namespace Tests\Setono\DoctrineORMBatcher;

use Doctrine\Common\DataFixtures\Purger\ORMPurger;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Tools\SchemaTool;
use Doctrine\ORM\Tools\Setup;
use PHPUnit\Framework\TestCase;

abstract class EntityManagerAwareTestCase extends TestCase
{
    protected EntityManagerInterface $entityManager;

    protected ORMPurger $purger;

    public function setUp(): void
    {
        parent::setUp();

        $config = Setup::createAnnotationMetadataConfiguration([__DIR__ . '/Entity'], true);

        $this->entityManager = EntityManager::create([
            'driver' => 'pdo_sqlite',
            'path' => __DIR__ . '/db.sqlite',
        ], $config);

        $metadata = $this->entityManager->getMetadataFactory()->getAllMetadata();

        $schemaTool = new SchemaTool($this->entityManager);
        $schemaTool->updateSchema($metadata);

        $this->purger = new ORMPurger($this->entityManager);
        $this->purger->purge();
    }
}
