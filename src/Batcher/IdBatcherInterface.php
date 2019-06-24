<?php

namespace Setono\DoctrineORMBatch\Batcher;

interface IdBatcherInterface
{
    public function getBatches(): iterable;
}
