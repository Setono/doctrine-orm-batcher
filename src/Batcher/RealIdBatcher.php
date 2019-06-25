<?php

declare(strict_types=1);

namespace Setono\DoctrineORMBatcher\Batcher;

use Doctrine\ORM\Mapping\MappingException;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\ORM\NoResultException;
use Safe\Exceptions\StringsException;
use Setono\DoctrineORMBatcher\Batch\Batch;
use function Safe\sprintf;

final class RealIdBatcher extends IdBatcher implements RealIdBatcherInterface
{
    /**
     * @return iterable|Batch[]
     *
     * @throws MappingException
     * @throws NoResultException
     * @throws NonUniqueResultException
     * @throws StringsException
     */
    public function getBatches(int $batchSize = 100): iterable
    {
        $identifier = $this->getIdentifier();
        $max = $this->getMax();

        $qb = $this->createQueryBuilder(sprintf('o.%s', $identifier));
        $qb->orderBy(sprintf('o.%s', $identifier))
            ->setMaxResults(1)
        ;

        $offset = 0;
        $lastId = 0;

        do {
            try {
                $qb->setFirstResult($offset);
                $id = (int) $qb->getQuery()->getSingleScalarResult();

                if (0 !== $lastId) {
                    yield new Batch($lastId, $id - 1);
                }

                $lastId = $id;

                $offset += $batchSize;
            } catch (NoResultException $e) {
                $id = null;
            }
        } while (null !== $id);

        if ($lastId <= $max) {
            yield new Batch($lastId, $max);
        }
    }
}
