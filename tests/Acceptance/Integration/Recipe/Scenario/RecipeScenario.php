<?php

declare(strict_types=1);

namespace Recipe\Tests\Acceptance\Integration\Recipe\Scenario;

use EventSauce\MessageOutbox\OutboxRelay;
use PHPUnit\Framework\MockObject\MockObject;
use Ramsey\Uuid\Uuid;
use Ramsey\Uuid\UuidFactoryInterface;
use Recipe\Domain\Bus\CommandBus;
use Recipe\Domain\UseCase\Recipe\Create\CreateARecipe;
use Recipe\Domain\UseCase\Recipe\Get\GetARecipeModel;
use Recipe\Domain\UseCase\Recipe\Get\GetARecipeService;
use Recipe\Infrastructure\Bus\MessengerCommandBus;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

/**
 * @group acceptance
 * @group integration
 * @group scenario
 */
final class RecipeScenario extends KernelTestCase
{
    private MessengerCommandBus $commandBus;
    private GetARecipeService $getARecipeService;
    private OutboxRelay $outboxRelay;
    private MockObject|UuidFactoryInterface $uuidFactory;

    protected function setUp(): void
    {
        $this->commandBus = self::getContainer()->get(CommandBus::class);
        $this->getARecipeService = self::getContainer()->get(GetARecipeService::class);
        $this->outboxRelay = self::getContainer()->get(OutboxRelay::class);
        $this->uuidFactory = $this->createMock(UuidFactoryInterface::class);

        self::getContainer()->set(UuidFactoryInterface::class, $this->uuidFactory);
    }

    /**
     * @test
     */
    public function whenICreateANewRecipeThenICanGetIt(): void
    {
        $this->uuidFactory->method('uuid4')->willReturn(Uuid::fromString('b2bdafb9-0a0b-47bf-978d-f1a2f8ad0974'));

        $this->commandBus->dispatch(
            new CreateARecipe('Orange and Cocoa Tart'),
        );

        $this->outboxRelay->publishBatch(10, 1);

        $actual = $this->getARecipeService->__invoke('b2bdafb9-0a0b-47bf-978d-f1a2f8ad0974');

        $expected = new GetARecipeModel(
            'b2bdafb9-0a0b-47bf-978d-f1a2f8ad0974',
            'Orange and Cocoa Tart',
        );
        self::assertEquals($expected, $actual);
    }
}
