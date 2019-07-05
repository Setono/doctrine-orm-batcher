<?php

declare(strict_types=1);

namespace Setono\DoctrineORMBatcher\Batch;

use Safe\Exceptions\StringsException;
use Setono\DoctrineORMBatcher\Exception\LowerBoundIsGreaterThanUpperBoundException;

final class RangeBatch
{
    /** @var int */
    private $lowerBound;

    /** @var int */
    private $upperBound;

    /**
     * @throws StringsException
     */
    public function __construct(int $lowerBound, int $upperBound)
    {
        if ($lowerBound > $upperBound) {
            throw new LowerBoundIsGreaterThanUpperBoundException($lowerBound, $upperBound);
        }

        $this->lowerBound = $lowerBound;
        $this->upperBound = $upperBound;
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
