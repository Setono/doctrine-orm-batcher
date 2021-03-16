<?php

declare(strict_types=1);

namespace Setono\DoctrineORMBatcher\Batch;

use Doctrine\ORM\QueryBuilder;
use Setono\DoctrineORMBatcher\Exception\LowerBoundIsGreaterThanUpperBoundException;

final class RangeBatch extends Batch implements RangeBatchInterface
{
    public const PARAMETER_LOWER_BOUND = 'lowerBound';

    public const PARAMETER_UPPER_BOUND = 'upperBound';

    private int $lowerBound;

    private int $upperBound;

    public function __construct(int $lowerBound, int $upperBound, QueryBuilder $qb)
    {
        if ($lowerBound > $upperBound) {
            throw new LowerBoundIsGreaterThanUpperBoundException($lowerBound, $upperBound);
        }

        $this->lowerBound = $lowerBound;
        $this->upperBound = $upperBound;

        $qb->setParameter(self::PARAMETER_LOWER_BOUND, $this->getLowerBound());
        $qb->setParameter(self::PARAMETER_UPPER_BOUND, $this->getUpperBound());

        /** @psalm-suppress ImpureMethodCall */
        parent::__construct($qb);
    }

    public function getLowerBound(): int
    {
        return $this->lowerBound;
    }

    public function getUpperBound(): int
    {
        return $this->upperBound;
    }
}
