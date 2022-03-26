<?php

declare(strict_types=1);

namespace Recipe\Domain\Consumer;

use EventSauce\EventSourcing\Message;
use EventSauce\EventSourcing\MessageConsumer;
use EventSauce\EventSourcing\MessageDispatcher;

final class DispatchConsumedMessages implements MessageConsumer
{
    public function __construct(private readonly MessageDispatcher $messageDispatcher)
    {
    }

    public function handle(Message $message): void
    {
        $this->messageDispatcher->dispatch($message);
    }
}
