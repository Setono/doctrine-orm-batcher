<?php

declare(strict_types=1);

namespace Setono\DoctrineORMBatcher\Batcher\Collection;

use Safe\Exceptions\StringsException;
use Setono\DoctrineORMBatcher\Batch\CollectionBatch;

final class ObjectCollectionBatcher extends CollectionBatcher
{
    /**
     * @return iterable|CollectionBatch[]
     *
     * @throws StringsException
     */
    public function getBatches(int $batchSize = 100): iterable
    {
        $result = $this->getResult(null, $batchSize);

        foreach ($result as $objects) {
            yield new CollectionBatch($objects, $this->getBatchableQueryBuilder());
        }
    }
}
