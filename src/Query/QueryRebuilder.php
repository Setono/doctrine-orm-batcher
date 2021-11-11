<?php

declare(strict_types=1);

namespace Setono\DoctrineORMBatcher\Query;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Query;
use Doctrine\Persistence\ManagerRegistry;
use Setono\DoctrineObjectManagerTrait\ORM\ORMManagerTrait;
use Setono\DoctrineORMBatcher\Batch\BatchInterface;
use Setono\DoctrineORMBatcher\Batcher\Batcher;

final class QueryRebuilder implements QueryRebuilderInterface
{
    use ORMManagerTrait;

    public function __construct(ManagerRegistry $managerRegistry)
    {
        $this->managerRegistry = $managerRegistry;
    }

    public function rebuild(BatchInterface $batch): Query
    {
        $manager = $this->getManager($batch->getClass());

        $q = $manager->createQuery($batch->getDql());
        $q->setParameters(new ArrayCollection($batch->getParameters()));
        $q->setParameter(Batcher::PARAMETER_DATA, $batch->getData());

        return $q;
    }
}
