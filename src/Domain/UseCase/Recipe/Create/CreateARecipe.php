<?php

declare(strict_types=1);

namespace Recipe\Domain\UseCase\Recipe\Create;

use Recipe\Domain\Bus\Command;

final class CreateARecipe implements Command
{
    public function __construct(
        public readonly string $name,
    ) {
    }
}
