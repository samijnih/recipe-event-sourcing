<?php

declare(strict_types=1);

namespace Recipe\Domain\UseCase\Recipe\Get;

use RuntimeException;

final class CannotGetARecipe extends RuntimeException
{
    public static function identifiedBy(string $id): self
    {
        return new self("Cannot get a recipe identified by '$id'.");
    }
}
