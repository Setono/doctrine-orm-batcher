<?php

declare(strict_types=1);

namespace Setono\DoctrineORMBatcher\Query;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Persistence\ManagerRegistry;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Query;
use Setono\DoctrineORMBatcher\Batch\BatchInterface;
use Setono\DoctrineORMBatcher\Exception\NoManagerException;

final class QueryRebuilder implements QueryRebuilderInterface
{
    /** @var ManagerRegistry */
    private $managerRegistry;

    public function __construct(ManagerRegistry $managerRegistry)
    {
        $this->managerRegistry = $managerRegistry;
    }

    /**
     * @throws \Safe\Exceptions\StringsException
     */
    public function rebuild(BatchInterface $batch): Query
    {
        $manager = $this->getManager($batch->getClass());

        $q = $manager->createQuery($batch->getDql());
        $q->setParameters(new ArrayCollection($batch->getParameters()));

        return $q;
    }

    /**
     * @throws \Safe\Exceptions\StringsException
     */
    private function getManager(string $class): EntityManagerInterface
    {
        /** @var EntityManagerInterface|null $manager */
        $manager = $this->managerRegistry->getManagerForClass($class);
        if (null === $manager) {
            throw new NoManagerException($class);
        }

        return $manager;
    }
}
