<?php

declare(strict_types=1);

namespace Tests\Setono\DoctrineORMBatcher\Entity;

/**
 * @Entity()
 * @Table(name="entity")
 */
class Entity
{
    /**
     * @Id()
     * @Column(type="integer")
     */
    protected int $id;

    /** @Column(type="boolean") */
    protected bool $enabled;

    public function __construct(int $id, bool $enabled = true)
    {
        $this->id = $id;
        $this->enabled = $enabled;
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function isEnabled(): bool
    {
        return $this->enabled;
    }
}
