<?php

declare(strict_types=1);

namespace Recipe\Domain\UseCase\Recipe\Get;

final class GetARecipeModel
{
    public function __construct(
        public readonly string $id,
        public readonly string $name,
    ) {
    }
}
