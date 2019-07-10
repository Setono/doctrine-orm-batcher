<?php

declare(strict_types=1);

namespace Setono\DoctrineORMBatcher\Exception;

use InvalidArgumentException;
use Safe\Exceptions\StringsException;
use function Safe\sprintf;

final class NoManagerException extends InvalidArgumentException implements ExceptionInterface
{
    /** @var string */
    private $class;

    /**
     * @throws StringsException
     */
    public function __construct(string $class)
    {
        parent::__construct(sprintf('No entity manager associated with the entity %s', $class));

        $this->class = $class;
    }

    public function getClass(): string
    {
        return $this->class;
    }
}
