<?php

declare(strict_types=1);

namespace Setono\DoctrineORMBatcher\Query;

use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Query;
use Setono\DoctrineORMBatcher\Batch\BatchInterface;

interface QueryRebuilderInterface
{
    public function rebuild(EntityManagerInterface $manager, BatchInterface $batch): Query;
}
