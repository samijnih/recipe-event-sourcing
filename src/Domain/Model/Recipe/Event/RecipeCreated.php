<?php

declare(strict_types=1);

namespace Recipe\Domain\Model\Recipe\Event;

use EventSauce\EventSourcing\Serialization\SerializablePayload;

final class RecipeCreated implements SerializablePayload
{
    public function __construct(
        public readonly string $id,
        public readonly string $name,
    ) {
    }

    public function toPayload(): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
        ];
    }

    public static function fromPayload(array $payload): SerializablePayload
    {
        return new self(
            $payload['id'],
            $payload['name'],
        );
    }
}
