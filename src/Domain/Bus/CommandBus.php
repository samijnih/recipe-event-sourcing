<?php

declare(strict_types=1);

namespace Recipe\Domain\Bus;

interface CommandBus
{
    public function dispatch(Command $command): void;
}
