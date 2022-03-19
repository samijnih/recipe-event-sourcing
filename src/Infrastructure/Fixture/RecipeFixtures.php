<?php

declare(strict_types=1);

namespace Recipe\Infrastructure\Fixture;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Recipe\Domain\ReadModel\Projection\Recipe\GetARecipeProjection;

final class RecipeFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        $recipe = new GetARecipeProjection(
            '09a91f17-a8d7-46f1-a26f-eae3bd7e7ffe',
            'recipeExists',
        );
        $manager->persist($recipe);

        $manager->flush();
    }
}
