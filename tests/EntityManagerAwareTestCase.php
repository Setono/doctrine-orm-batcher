<?php

declare(strict_types=1);

namespace Tests\Setono\DoctrineORMBatcher;

use Doctrine\Common\DataFixtures\Purger\ORMPurger;
use Doctrine\DBAL\DriverManager;
use Doctrine\ORM\Configuration;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\ORMSetup;
use Doctrine\ORM\Tools\SchemaTool;
use PHPUnit\Framework\TestCase;

abstract class EntityManagerAwareTestCase extends TestCase
{
    protected EntityManagerInterface $entityManager;

    protected ORMPurger $purger;

    public function setUp(): void
    {
        parent::setUp();

        $config = $this->getOrmConfiguration();

        $connection = DriverManager::getConnection([
            'driver' => 'pdo_sqlite',
            'path' => __DIR__ . '/db.sqlite',
        ], $config);

        $this->entityManager = new EntityManager($connection, $config);

        $metadata = $this->entityManager->getMetadataFactory()->getAllMetadata();

        $schemaTool = new SchemaTool($this->entityManager);
        $schemaTool->updateSchema($metadata);

        $this->purger = new ORMPurger($this->entityManager);
        $this->purger->purge();
    }

    protected function getOrmConfiguration(): Configuration
    {
        if (method_exists(ORMSetup::class, 'createAnnotationMetadataConfiguration')) {
            return ORMSetup::createAnnotationMetadataConfiguration([__DIR__ . '/Entity'], true);
        }
        if (method_exists(ORMSetup::class, 'createAttributeMetadataConfiguration')) {
            return ORMSetup::createAttributeMetadataConfiguration([__DIR__ . '/Entity'], true);
        }

        throw new \RuntimeException('Could not create ORM configuration');
    }
}
