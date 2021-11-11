<?php

declare(strict_types=1);

namespace Setono\DoctrineORMBatcher\Batcher;

use Doctrine\ORM\QueryBuilder;
use Doctrine\Persistence\Mapping\ClassMetadata;
use InvalidArgumentException;
use Setono\DoctrineORMBatcher\Batch\Batch;
use Webmozart\Assert\Assert;

final class Batcher implements BatcherInterface
{
    // the 'sdob' stands for 'setono doctrine orm batcher' and it's just a way to make the parameters unique
    // so they don't interfere with any parameters you've set yourself :)
    public const PARAMETER_DATA = 'sdob_data';
    private const PARAMETER_LAST_ID = 'sdob_last_id';

    private QueryBuilder $qb;

    private string $identifier;

    private bool $flushOnBatch = true;

    private bool $clearOnBatch = true;

    private ClassMetadata $rootEntityMetadata;

    private string $rootAlias;

    private ?int $count = null;

    public function __construct(QueryBuilder $qb)
    {
        $rootAliases = $qb->getRootAliases();
        if (1 !== count($rootAliases)) {
            throw new InvalidArgumentException(sprintf(
                'The query builder must have exactly one root alias. Your query builder has %d root aliases',
                count($rootAliases)
            ));
        }

        $rootEntity = $qb->getRootEntities()[0];
        $rootEntityMetadata = $qb->getEntityManager()->getClassMetadata($rootEntity);

        if($rootEntityMetadata->isIdentifierComposite) {
            throw new InvalidArgumentException('This library only support non composite primary keys for now. Sorry :(');
        }

        // todo check for order by dql part since we override that

        $this->identifier = $rootEntityMetadata->getSingleIdentifierFieldName();
        $this->rootEntityMetadata = $rootEntityMetadata;
        $this->rootAlias = $rootAliases[0];

        // immediately we clone it so that changes to the query builder outside of the batcher doesn't affect it
        $this->qb = clone $qb;
    }

    public function getBatches(int $batchSize = 100): iterable
    {
        $qb = $this->getQueryBuilder();

        $qb->orderBy(sprintf('%s.%s', $this->rootAlias, $this->identifier), 'ASC')
            ->setMaxResults($batchSize)
        ;

        $batch = [];
        $lastIdentifier = null;

        do {
            if (null !== $lastIdentifier) {
                $qb->setParameter(self::PARAMETER_LAST_ID, $lastIdentifier);
            }

            $result = $qb->getQuery()->getResult();
            Assert::isArray($result, sprintf('The DQL query "%s" did not result in an array when executing', $qb->getDQL()));
            if ([] === $result) {
                break;
            }

            foreach ($result as $item) {
                Assert::true(is_array($item) || is_object($item));

                $batch[] = $item;
            }

            if (null === $lastIdentifier) {
                $qb->andWhere(sprintf('%s.%s > :%s', $this->rootAlias, $this->identifier, self::PARAMETER_LAST_ID));
            }

            $lastIdentifier = $this->getIdentifierValueFromItem($item);

            $this->flush();
            $this->clear();

            yield new Batch($batch, $this->getBatchableQueryBuilder());
        } while (true);

        $this->flush();
        $this->clear();
    }

    public function getBatchCount(int $batchSize = 100): int
    {
        return (int) ceil($this->getCount() / $batchSize);
    }

    public function flushOnBatch(): void
    {
        $this->flushOnBatch = true;
    }

    public function doNotFlushOnBatch(): void
    {
        $this->flushOnBatch = false;
    }

    public function clearOnBatch(): void
    {
        $this->clearOnBatch = true;
    }

    public function doNotClearOnBatch(): void
    {
        $this->clearOnBatch = false;
    }

    private function flush(): void
    {
        if (!$this->flushOnBatch) {
            return;
        }

        $this->qb->getEntityManager()->flush();
    }

    private function clear(): void
    {
        if (!$this->clearOnBatch) {
            return;
        }

        $this->qb->getEntityManager()->clear();
    }

    /**
     * This is made to avoid side effects by passing around the query builder object.
     */
    private function getQueryBuilder(): QueryBuilder
    {
        return clone $this->qb;
    }

    /**
     * @param array|object $item
     * @return string|int
     */
    private function getIdentifierValueFromItem($item)
    {
        if(is_object($item)) {
            $lastId = $this->rootEntityMetadata->getIdentifierValues($item)[$this->identifier];
        } else {
            Assert::keyExists($item, $this->identifier, 'The identifier of your root entity needs to be part of your select statement');

            /** @var mixed $lastId */
            $lastId = $item[$this->identifier];
        }

        Assert::true(is_int($lastId) || is_string($lastId));

        return $lastId;
    }

    /**
     * This will return a query builder where the constraints for the respective batcher are added.
     */
    private function getBatchableQueryBuilder(): QueryBuilder
    {
        $qb = $this->getQueryBuilder();
        $qb->andWhere(sprintf('%s.%s IN(:%s)', $this->rootAlias, $this->identifier, self::PARAMETER_DATA));

        return $qb;
    }

    private function getCount(): int
    {
        if (null === $this->count) {
            $this->initCount();
        }

        return $this->count;
    }

    /**
     * @psalm-assert int $this->count
     */
    private function initCount(): void
    {
        $qb = $this->getQueryBuilder();
        $qb->select(sprintf('COUNT(%s)', $this->rootAlias));

        $this->count = (int) $qb->getQuery()->getSingleScalarResult();
    }
}
