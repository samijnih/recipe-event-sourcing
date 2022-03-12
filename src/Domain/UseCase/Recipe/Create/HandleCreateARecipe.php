<?php

declare(strict_types=1);

namespace Recipe\Domain\UseCase\Recipe\Create;

use Ramsey\Uuid\UuidFactoryInterface;
use Recipe\Domain\Model\Recipe\Recipe;
use Recipe\Domain\Model\Recipe\RecipeRepository;
use Recipe\Domain\Model\Recipe\RecipeUuid;

final class HandleCreateARecipe
{
    public function __construct(
        private RecipeRepository $recipeRepository,
        private UuidFactoryInterface $uuidFactory,
    ) {
    }

    public function __invoke(CreateARecipe $command): void
    {
        $recipe = Recipe::create(
            RecipeUuid::fromString($this->uuidFactory->uuid4()->toString()),
            $command->name
        );

        $this->recipeRepository->persist($recipe);
    }
}
