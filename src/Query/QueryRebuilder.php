<?php

declare(strict_types=1);

namespace Setono\DoctrineORMBatcher\Query;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Query;
use Setono\DoctrineORMBatcher\Batch\BatchInterface;

final class QueryRebuilder implements QueryRebuilderInterface
{
    public function rebuild(EntityManagerInterface $manager, BatchInterface $batch): Query
    {
        $q = $manager->createQuery($batch->getDql());
        $q->setParameters(new ArrayCollection($batch->getParameters()));

        return $q;
    }
}
