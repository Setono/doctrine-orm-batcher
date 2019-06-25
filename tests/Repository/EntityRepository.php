<?php

declare(strict_types=1);

namespace Tests\Setono\DoctrineORMBatcher\Repository;

use Doctrine\ORM\EntityRepository as BaseEntityRepository;
use Setono\DoctrineORMBatcher\Repository\BatchableInterface;
use Setono\DoctrineORMBatcher\Repository\BatchableTrait;

final class EntityRepository extends BaseEntityRepository implements BatchableInterface
{
    use BatchableTrait;
}
