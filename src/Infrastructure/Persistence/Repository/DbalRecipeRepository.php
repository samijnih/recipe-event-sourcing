<?php

declare(strict_types=1);

namespace Recipe\Infrastructure\Persistence\Repository;

use Recipe\Domain\Model\Recipe\RecipeRepository;
use Recipe\Domain\Model\Shared\EventSourcedAggregateRootRepository;

final class DbalRecipeRepository extends EventSourcedAggregateRootRepository implements RecipeRepository
{
}
