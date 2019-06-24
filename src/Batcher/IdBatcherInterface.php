<?php

namespace Setono\DoctrineORMBatcher\Batcher;

interface IdBatcherInterface
{
    public function getBatches(): iterable;
}
