<?php

declare(strict_types=1);

namespace Setono\DoctrineORMBatcher\Exception;

use RuntimeException;
use Safe\Exceptions\StringsException;
use function Safe\sprintf;

final class WrongFieldTypeException extends RuntimeException implements ExceptionInterface
{
    /** @var string */
    private $fieldName;

    /** @var string */
    private $class;

    /** @var string */
    private $expectedType;

    /**
     * @throws StringsException
     */
    public function __construct(string $fieldName, string $class, string $expectedType)
    {
        parent::__construct(sprintf('The field "%s" on class %s did not have the expected type; "%s"', $fieldName, $class, $expectedType));

        $this->fieldName = $fieldName;
        $this->class = $class;
        $this->expectedType = $expectedType;
    }

    public function getFieldName(): string
    {
        return $this->fieldName;
    }

    public function getClass(): string
    {
        return $this->class;
    }

    public function getExpectedType(): string
    {
        return $this->expectedType;
    }
}
