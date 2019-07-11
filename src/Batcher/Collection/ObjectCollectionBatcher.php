<?php

declare(strict_types=1);

namespace Setono\DoctrineORMBatcher\Batcher\Collection;

use Doctrine\Common\Persistence\Mapping\MappingException;
use Safe\Exceptions\StringsException;
use Setono\DoctrineORMBatcher\Batch\CollectionBatch;
use Setono\DoctrineORMBatcher\Batch\CollectionBatchInterface;

final class ObjectCollectionBatcher extends CollectionBatcher
{
    /**
     * @return iterable|CollectionBatchInterface[]
     *
     * @throws StringsException
     * @throws MappingException
     */
    public function getBatches(int $batchSize = 100): iterable
    {
        $result = $this->getResult(null, $batchSize);

        foreach ($result as $objects) {
            yield new CollectionBatch($objects, $this->getBatchableQueryBuilder());
        }
    }
}
