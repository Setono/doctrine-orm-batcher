<?php

declare(strict_types=1);

namespace Setono\DoctrineORMBatcher\Query;

use Doctrine\ORM\Query;
use Setono\DoctrineORMBatcher\Batch\BatchInterface;

interface QueryRebuilderInterface
{
    public function rebuild(BatchInterface $batch): Query;
}
