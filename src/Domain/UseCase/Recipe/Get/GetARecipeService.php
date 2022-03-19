<?php

declare(strict_types=1);

namespace Recipe\Domain\UseCase\Recipe\Get;

use Recipe\Domain\ReadModel\Repository\RecipeReadModelRepository;
use Throwable;

final class GetARecipeService
{
    public function __construct(private RecipeReadModelRepository $recipeReadModelRepository)
    {
    }

    public function __invoke(string $id): GetARecipeModel
    {
        try {
            return $this->recipeReadModelRepository->getARecipe($id);
        } catch (Throwable) {
            throw CannotGetARecipe::identifiedBy($id);
        }
    }
}
