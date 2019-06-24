<?php

namespace Tests\Setono\DoctrineORMBatch\Stub\Entity;

/**
 * @Entity
 * @Table(name="entity")
 */
class ValidEntity
{
    /**
     * @Id()
     * @Column(type="integer")
     */
    protected $id;

    public function __construct(int $id)
    {
        $this->id = $id;
    }
}
