<?php

declare(strict_types=1);

namespace Tests\Setono\DoctrineORMBatcher\Entity;

/**
 * @Entity(repositoryClass="Tests\Setono\DoctrineORMBatcher\Repository\EntityRepository")
 * @Table(name="entity")
 */
class Entity
{
    /**
     * @Id()
     * @Column(type="integer")
     *
     * @var int
     */
    protected $id;

    public function __construct(int $id)
    {
        $this->id = $id;
    }

    public function getId(): int
    {
        return $this->id;
    }
}
