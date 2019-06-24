<?php

namespace Setono\DoctrineORMBatcher\Batch;

final class Batch
{
    /**
     * @var int
     */
    private $lowerBound;

    /**
     * @var int
     */
    private $upperBound;

    public function __construct(int $lowerBound, int $upperBound)
    {
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
