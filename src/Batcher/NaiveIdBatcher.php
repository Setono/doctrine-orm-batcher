<?php

namespace Setono\DoctrineORMBatcher\Batcher;

use Doctrine\ORM\Mapping\MappingException;
use Doctrine\ORM\NoResultException;
use Setono\DoctrineORMBatcher\Batch\Batch;

final class NaiveIdBatcher extends IdBatcher
{
    /**
     * @throws NoResultException
     * @throws MappingException
     *
     * @return iterable|Batch[]
     */
    public function getBatches(int $batchSize = 100): iterable
    {
        $qb = $this->getManager()->createQueryBuilder();
        $identifier = $this->getIdentifier();

        $qb->select(sprintf('MIN(o.%s) as min, MAX(o.%s) as max', $identifier, $identifier))
            ->from($this->class, 'o')
        ;

        $res = $qb->getQuery()->getScalarResult()[0];

        if (null === $res['min'] || null === $res['max']) {
            throw new NoResultException();
        }

        yield from $this->numberBatcher->getBatches($res['min'], $res['max'], $batchSize);
    }
}
