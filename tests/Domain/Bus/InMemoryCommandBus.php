<?php

declare(strict_types=1);

namespace Recipe\Tests\Domain\Bus;

use Recipe\Domain\Bus\Command;
use Recipe\Domain\Bus\CommandBus;

final class InMemoryCommandBus implements CommandBus
{
    public function __construct(private readonly array $handlers)
    {
    }

    public function dispatch(Command $command): void
    {
        if (false === array_key_exists($command::class, $this->handlers)) {
            return;
        }

        $this->handlers[$command::class]($command);
    }
}
