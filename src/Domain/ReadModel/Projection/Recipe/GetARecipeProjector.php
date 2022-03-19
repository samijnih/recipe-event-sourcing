<?php

declare(strict_types=1);

namespace Recipe\Domain\ReadModel\Projection\Recipe;

interface GetARecipeProjector
{
    public function saveGetARecipe(GetARecipeProjection $getARecipeProjection): void;
}
