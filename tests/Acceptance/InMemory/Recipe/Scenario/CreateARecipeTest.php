<?php

declare(strict_types=1);

namespace Recipe\Tests\Acceptance\InMemory\Recipe\Scenario;

use Ramsey\Uuid\Uuid;
use Recipe\Domain\Model\Recipe\Event\RecipeCreated;
use Recipe\Domain\UseCase\Recipe\Create\CreateARecipe;
use Recipe\Tests\Acceptance\InMemory\Recipe\RecipeTest;

/**
 * @coversDefaultClass \Recipe\Domain\UseCase\Recipe\Create\HandleCreateARecipe
 * @group acceptance
 * @group in-memory
 */
final class CreateARecipeTest extends RecipeTest
{
    /**
     * @test
     * @covers ::__invoke
     */
    public function iCanCreateARecipe(): void
    {
        $this->uuidFactory->method('uuid4')->willReturn(Uuid::fromString($this->aggregateRootId()->toString()));

        $command = new CreateARecipe('Saumon au four');

        $this
            ->when($command)
            ->then(new RecipeCreated('Saumon au four'))
        ;
    }
}
