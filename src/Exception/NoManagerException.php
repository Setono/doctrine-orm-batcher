<?php

declare(strict_types=1);

namespace Setono\DoctrineORMBatcher\Exception;

use RuntimeException;
use Safe\Exceptions\StringsException;
use function Safe\sprintf;

final class NoManagerException extends RuntimeException implements ExceptionInterface
{
    /** @var string */
    private $class;

    /**
     * @throws StringsException
     */
    public function __construct(string $class)
    {
        parent::__construct(sprintf('No manager associated with the class %s', $class));

        $this->class = $class;
    }

    public function getClass(): string
    {
        return $this->class;
    }
}
