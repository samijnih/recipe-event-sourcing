<?php

declare(strict_types=1);

namespace Recipe\Domain\ReadModel\Repository;

use Recipe\Domain\UseCase\Recipe\Get\GetARecipeModel;

interface RecipeReadModelRepository
{
    public function getARecipe(string $id): GetARecipeModel;
}
