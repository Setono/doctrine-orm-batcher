<?php

declare(strict_types=1);

namespace Setono\DoctrineORMBatcher\Batch;

interface BatchInterface
{
    /**
     * This is the DQL needed to fetch this particular batch.
     */
    public function getDql(): string;

    /**
     * These are the parameter values that needs to be set when executing the DQL.
     */
    public function getParameters(): array;
}
