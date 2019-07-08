<?php

declare(strict_types=1);

namespace Setono\DoctrineORMBatcher\Batch;

use Doctrine\ORM\QueryBuilder;
use Safe\Exceptions\StringsException;
use Setono\DoctrineORMBatcher\Exception\LowerBoundIsGreaterThanUpperBoundException;

final class RangeBatch extends Batch
{
    public const PARAMETER_LOWER_BOUND = 'lowerBound';

    public const PARAMETER_UPPER_BOUND = 'upperBound';

    /** @var int */
    private $lowerBound;

    /** @var int */
    private $upperBound;

    /**
     * @throws StringsException
     */
    public function __construct(int $lowerBound, int $upperBound, QueryBuilder $qb)
    {
        if ($lowerBound > $upperBound) {
            throw new LowerBoundIsGreaterThanUpperBoundException($lowerBound, $upperBound);
        }

        $this->lowerBound = $lowerBound;
        $this->upperBound = $upperBound;

        $qb->setParameters([
            self::PARAMETER_LOWER_BOUND => $this->getLowerBound(),
            self::PARAMETER_UPPER_BOUND => $this->getUpperBound(),
        ]);

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
