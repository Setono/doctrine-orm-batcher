<?php

declare(strict_types=1);

namespace Setono\DoctrineORMBatcher\Exception;

use InvalidArgumentException;
use Safe\Exceptions\StringsException;
use function Safe\sprintf;

final class LowerBoundIsGreaterThanUpperBoundException extends InvalidArgumentException implements ExceptionInterface
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
        parent::__construct(sprintf('The lower bound, %d, is greater than the upper bound, %d', $lowerBound, $upperBound));

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
