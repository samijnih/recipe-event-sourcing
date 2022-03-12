<?php

declare(strict_types=1);

namespace Recipe\Infrastructure\Bus;

use Recipe\Domain\Bus\Command;
use Recipe\Domain\Bus\CommandBus;
use Symfony\Component\Messenger\Envelope;
use Symfony\Component\Messenger\MessageBusInterface;

final class MessengerCommandBus implements CommandBus
{
    public function __construct(private readonly MessageBusInterface $messageBus)
    {
    }

    public function dispatch(Command $command): void
    {
        $this->messageBus->dispatch(
            Envelope::wrap($command),
        );
    }
}
