<?php

declare(strict_types=1);

namespace Recipe\Domain\Listener\Recipe;

use EventSauce\EventSourcing\EventHandlingMessageConsumer;
use EventSauce\EventSourcing\MessageConsumer;
use Recipe\Domain\Model\Recipe\Event\RecipeCreated;
use Recipe\Domain\ReadModel\Projection\Recipe\GetARecipeProjection;
use Recipe\Domain\ReadModel\Projection\Recipe\GetARecipeProjector;

final class RecipeCreatedListener extends EventHandlingMessageConsumer implements MessageConsumer
{
    public function __construct(
        private GetARecipeProjector $projector,
    ) {
    }

    public function handleRecipeCreated(RecipeCreated $recipeCreated): void
    {
        $this->projector->saveGetARecipe(
            new GetARecipeProjection(
                $recipeCreated->id,
                $recipeCreated->name,
            ),
        );
    }
}
