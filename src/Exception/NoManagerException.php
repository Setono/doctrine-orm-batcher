<?php

declare(strict_types=1);

namespace Setono\DoctrineORMBatcher\Exception;

use InvalidArgumentException;

final class NoManagerException extends InvalidArgumentException implements ExceptionInterface
{
    private string $class;

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
