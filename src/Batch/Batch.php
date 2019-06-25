<?php

declare(strict_types=1);

namespace Setono\DoctrineORMBatcher\Batch;

use function Safe\sprintf;

final class Batch
{
    /** @var int */
    private $lowerBound;

    /** @var int */
    private $upperBound;

    public function __construct(int $lowerBound, int $upperBound)
    {
        if ($lowerBound > $upperBound) {
            throw new \InvalidArgumentException(sprintf('Lower bound %s is greater than the upper bound %s', $lowerBound, $upperBound));
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
