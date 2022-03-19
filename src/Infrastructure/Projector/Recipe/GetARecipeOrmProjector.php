<?php

declare(strict_types=1);

namespace Recipe\Infrastructure\Projector\Recipe;

use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ManagerRegistry;
use Recipe\Domain\ReadModel\Projection\Recipe\GetARecipeProjection;
use Recipe\Domain\ReadModel\Projection\Recipe\GetARecipeProjector;

final class GetARecipeOrmProjector implements GetARecipeProjector
{
    private readonly EntityManagerInterface $em;

    public function __construct(
        ManagerRegistry $registry,
    ) {
        $this->em = $registry->getManagerForClass(GetARecipeProjection::class);
    }

    public function saveGetARecipe(GetARecipeProjection $getARecipeProjection): void
    {
        $this->em->persist($getARecipeProjection);
        $this->em->flush();
    }
}
