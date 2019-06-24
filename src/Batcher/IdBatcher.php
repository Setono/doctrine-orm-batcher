<?php

namespace Setono\DoctrineORMBatcher\Batcher;

use Doctrine\Common\Persistence\ManagerRegistry;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Mapping\MappingException;

abstract class IdBatcher implements IdBatcherInterface
{
    /**
     * @var ManagerRegistry
     */
    private $managerRegistry;

    /**
     * @var EntityManagerInterface
     */
    private $manager;

    /**
     * @var string
     */
    private $identifier;

    /**
     * @var string
     */
    protected $class;

    /**
     * @var NumberBatcherInterface
     */
    protected $numberBatcher;

    public function __construct(ManagerRegistry $managerRegistry, string $class, NumberBatcherInterface $numberBatcher)
    {
        $this->managerRegistry = $managerRegistry;
        $this->class = $class;
        $this->numberBatcher = $numberBatcher;
    }

    /**
     * @throws MappingException
     */
    protected function getIdentifier(): string
    {
        if (null === $this->identifier) {
            $metaData = $this->getManager()->getClassMetadata($this->class);

            $identifier = $metaData->getSingleIdentifierFieldName();

            if ('integer' !== $metaData->getTypeOfField($identifier)) {
                throw new \RuntimeException(sprintf('The %s only works with identifiers that are integers',
                    self::class));
            }

            $this->identifier = $identifier;
        }

        return $this->identifier;
    }

    protected function getManager(): EntityManagerInterface
    {
        if (null === $this->manager) {
            /** @var EntityManagerInterface|null $manager */
            $manager = $this->managerRegistry->getManagerForClass($this->class);

            if (!$manager instanceof EntityManagerInterface) {
                throw new \RuntimeException('This library only works with the doctrine/orm library'); // todo better exception
            }

            $this->manager = $manager;
        }

        return $this->manager;
    }
}
