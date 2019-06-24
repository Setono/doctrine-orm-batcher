<?php

declare(strict_types=1);

namespace Setono\DoctrineORMBatcher\Batcher;

use Doctrine\Common\Persistence\ManagerRegistry;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Mapping\MappingException;
use Doctrine\ORM\NoResultException;
use Doctrine\ORM\QueryBuilder;
use Safe\Exceptions\StringsException;
use function Safe\sprintf;

abstract class IdBatcher implements IdBatcherInterface
{
    /**
     * @var ManagerRegistry
     */
    private $managerRegistry;

    /**
     * @var EntityManagerInterface
     */
    private $manager;

    /**
     * @var string
     */
    private $identifier;

    /**
     * @var string
     */
    private $class;

    /**
     * @var int
     */
    private $min;

    /**
     * @var int
     */
    private $max;

    public function __construct(ManagerRegistry $managerRegistry, string $class)
    {
        $this->managerRegistry = $managerRegistry;
        $this->class = $class;
    }

    /**
     * @throws MappingException
     * @throws StringsException
     */
    protected function getIdentifier(): string
    {
        if (null === $this->identifier) {
            $metaData = $this->getManager()->getClassMetadata($this->class);

            $identifier = $metaData->getSingleIdentifierFieldName();

            if ('integer' !== $metaData->getTypeOfField($identifier)) {
                throw new \RuntimeException(sprintf('The %s only works with identifiers that are integers',
                    self::class));
            }

            $this->identifier = $identifier;
        }

        return $this->identifier;
    }

    protected function createQueryBuilder(string $select, string $alias = 'o'): QueryBuilder
    {
        $manager = $this->getManager();
        $qb = $manager->createQueryBuilder();
        $qb->select($select)
            ->from($this->class, $alias);

        return $qb;
    }

    private function getManager(): EntityManagerInterface
    {
        if (null === $this->manager) {
            /** @var EntityManagerInterface|null $manager */
            $manager = $this->managerRegistry->getManagerForClass($this->class);

            if (!$manager instanceof EntityManagerInterface) {
                throw new \RuntimeException('This library only works with the doctrine/orm library'); // todo better exception
            }

            $this->manager = $manager;
        }

        return $this->manager;
    }

    /**
     * @throws MappingException
     * @throws NoResultException
     */
    protected function getMin(): int
    {
        if (null === $this->min) {
            $this->initMinMax();
        }

        return $this->min;
    }

    /**
     * @throws MappingException
     * @throws NoResultException
     */
    protected function getMax(): int
    {
        if (null === $this->max) {
            $this->initMinMax();
        }

        return $this->max;
    }

    /**
     * @throws MappingException
     * @throws NoResultException
     */
    private function initMinMax(): void
    {
        $qb = $this->getManager()->createQueryBuilder();
        $identifier = $this->getIdentifier();

        $qb->select(sprintf('MIN(o.%s) as min, MAX(o.%s) as max', $identifier, $identifier))
            ->from($this->class, 'o')
        ;

        $res = $qb->getQuery()->getScalarResult();
        if (count($res) < 1) {
            throw new NoResultException();
        }

        $row = $res[0];

        if (null === $row['min'] || null === $row['max']) {
            throw new NoResultException();
        }

        $this->min = (int) $row['min'];
        $this->max = (int) $row['max'];
    }
}
