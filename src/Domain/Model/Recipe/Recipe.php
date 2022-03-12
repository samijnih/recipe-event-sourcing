<?php

declare(strict_types=1);

namespace Recipe\Domain\Model\Recipe;

use EventSauce\EventSourcing\AggregateRoot;
use EventSauce\EventSourcing\AggregateRootBehaviour;
use Recipe\Domain\Model\Recipe\Event\RecipeCreated;

final class Recipe implements AggregateRoot
{
    use AggregateRootBehaviour;

    private string $name;

    public static function create(RecipeId $id, string $name): self
    {
        $recipe = new self($id);
        $recipe->recordThat(new RecipeCreated($name));

        return $recipe;
    }

    public function applyRecipeCreated(RecipeCreated $event): void
    {
        $this->name = $event->name;
    }

    public function name(): string
    {
        return $this->name;
    }
}
