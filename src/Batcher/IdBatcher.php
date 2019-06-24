<?php

namespace Setono\DoctrineORMBatch\Batcher;

use Doctrine\Common\Persistence\ManagerRegistry;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\NoResultException;

final class IdBatcher implements IdBatcherInterface
{
    /**
     * @var ManagerRegistry
     */
    private $managerRegistry;

    /**
     * @var string
     */
    private $class;
    /**
     * @var NumberBatcherInterface
     */
    private $numberBatcher;

    public function __construct(ManagerRegistry $managerRegistry, string $class, NumberBatcherInterface $numberBatcher)
    {
        $this->managerRegistry = $managerRegistry;
        $this->class = $class;
        $this->numberBatcher = $numberBatcher;
    }

    public function getBatches(): iterable
    {
        /** @var EntityManagerInterface|null $manager */
        $manager = $this->managerRegistry->getManagerForClass($this->class);

        if(!$manager instanceof EntityManagerInterface) {
            throw new \RuntimeException('This library only works with the doctrine/orm library'); // todo better exception
        }

        $metaData = $manager->getClassMetadata($this->class);

        $identifier = $metaData->getSingleIdentifierFieldName();

        if($metaData->getTypeOfField($identifier) !== 'integer') {
            throw new \RuntimeException(sprintf('The %s only works with identifiers that are integers', self::class));
        }

        $qb = $manager->createQueryBuilder();
        $qb->select(sprintf('MIN(o.%s) as min, MAX(o.%s) as max', $identifier, $identifier))
            ->from($this->class, 'o')
        ;

        $res = $qb->getQuery()->getScalarResult()[0];

        if(null === $res['min'] || null === $res['max']) {
            throw new NoResultException();
        }

        yield from $this->numberBatcher->getBatches($res['min'], $res['max']);
    }
}
