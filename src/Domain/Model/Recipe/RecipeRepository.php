<?php

declare(strict_types=1);

namespace Recipe\Domain\Model\Recipe;

use EventSauce\EventSourcing\AggregateRootRepository;

interface RecipeRepository extends AggregateRootRepository
{
}
