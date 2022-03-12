<?php

declare(strict_types=1);

namespace Recipe\Tests\Acceptance\Integration\Recipe\Scenario;

use PHPUnit\Framework\MockObject\MockObject;
use Ramsey\Uuid\Uuid;
use Ramsey\Uuid\UuidFactoryInterface;
use Recipe\Domain\Bus\CommandBus;
use Recipe\Domain\Model\Recipe\Recipe;
use Recipe\Domain\Model\Recipe\RecipeRepository;
use Recipe\Domain\Model\Recipe\RecipeUuid;
use Recipe\Domain\UseCase\Recipe\Create\CreateARecipe;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

/**
 * @coversDefaultClass \Recipe\Domain\UseCase\Recipe\Create\HandleCreateARecipe
 * @group acceptance
 * @group integration
 */
final class CreateARecipeTest extends KernelTestCase
{
    private RecipeRepository $recipeRepository;
    private CommandBus $messageBus;
    private MockObject|UuidFactoryInterface $uuidFactory;

    protected function setUp(): void
    {
        $this->recipeRepository = self::getContainer()->get(RecipeRepository::class);
        $this->messageBus = self::getContainer()->get(CommandBus::class);
        $this->uuidFactory = $this->createMock(UuidFactoryInterface::class);

        self::getContainer()->set(UuidFactoryInterface::class, $this->uuidFactory);
    }

    /**
     * @test
     */
    public function iCanCreateARecipe(): void
    {
        // Arrange
        $this->uuidFactory->method('uuid4')->willReturn(Uuid::fromString('b2bdafb9-0a0b-47bf-978d-f1a2f8ad0974'));

        // Act
        $command = new CreateARecipe(
            'Chicken Tendori',
        );
        $this->messageBus->dispatch($command);

        // Assert
        /** @var Recipe $actual */
        $actual = $this->recipeRepository->retrieve(RecipeUuid::fromString('b2bdafb9-0a0b-47bf-978d-f1a2f8ad0974'));
        self::assertSame($command->name, $actual->name());
    }
}
