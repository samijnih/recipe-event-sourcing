<?php

declare(strict_types=1);

namespace Recipe\Domain\Model\Recipe;

use Recipe\Domain\Model\Shared\AbstractUuid;

final class RecipeUuid extends AbstractUuid implements RecipeId
{
}
