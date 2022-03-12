<?php

declare(strict_types=1);

namespace Recipe\Domain\Model\Shared;

use EventSauce\EventSourcing\AggregateRootId;
use Ramsey\Uuid\Uuid;
use Ramsey\Uuid\UuidInterface;

abstract class AbstractUuid implements AggregateRootId
{
    public function __construct(private readonly UuidInterface $id)
    {
    }

    public function toString(): string
    {
        return $this->id->toString();
    }

    public static function fromString(string $aggregateRootId): AggregateRootId
    {
        return new static(Uuid::fromString($aggregateRootId));
    }
}
