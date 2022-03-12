<?php

declare(strict_types=1);

namespace Recipe\Infrastructure\Persistence\Repository;

use EventSauce\EventSourcing\EventSourcedAggregateRootRepository;
use Recipe\Domain\Model\Recipe\RecipeRepository;

final class DbalRecipeRepository extends EventSourcedAggregateRootRepository implements RecipeRepository
{
}
