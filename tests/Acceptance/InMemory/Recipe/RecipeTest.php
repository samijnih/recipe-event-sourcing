<?php

declare(strict_types=1);

namespace Recipe\Tests\Acceptance\InMemory\Recipe;

use EventSauce\EventSourcing\AggregateRootId;
use EventSauce\EventSourcing\AggregateRootRepository;
use EventSauce\EventSourcing\MessageDecorator;
use EventSauce\EventSourcing\MessageDispatcher;
use EventSauce\EventSourcing\MessageRepository;
use EventSauce\EventSourcing\TestUtilities\AggregateRootTestCase;
use PHPUnit\Framework\MockObject\MockObject;
use Ramsey\Uuid\UuidFactoryInterface;
use Recipe\Domain\Bus\Command;
use Recipe\Domain\Model\Recipe\Recipe;
use Recipe\Domain\Model\Recipe\RecipeRepository;
use Recipe\Domain\Model\Recipe\RecipeUuid;
use Recipe\Infrastructure\Persistence\Repository\DbalRecipeRepository;
use Recipe\Tests\Domain\Bus\InMemoryCommandBus;

abstract class RecipeTest extends AggregateRootTestCase
{
    protected AggregateRootRepository|RecipeRepository $repository;
    protected MockObject|UuidFactoryInterface $uuidFactory;

    /** {@inheritDoc} */
    protected function aggregateRootRepository(
        string $className,
        MessageRepository $repository,
        MessageDispatcher $dispatcher,
        MessageDecorator $decorator
    ): RecipeRepository {
        return new DbalRecipeRepository(
            $className,
            $repository,
            $dispatcher,
            $decorator
        );
    }

    protected function setUp(): void
    {
        $this->uuidFactory = $this->createMock(UuidFactoryInterface::class);
        $this->messageBus = new InMemoryCommandBus([
            \Recipe\Domain\UseCase\Recipe\Create\CreateARecipe::class => new \Recipe\Domain\UseCase\Recipe\Create\HandleCreateARecipe(
                $this->repository,
                $this->uuidFactory,
            ),
        ]);
    }

    protected function newAggregateRootId(): AggregateRootId
    {
        return RecipeUuid::fromString('11a0768b-1d3a-48ec-8638-38ec6264e22d');
    }

    protected function aggregateRootClassName(): string
    {
        return Recipe::class;
    }

    protected function handle(Command $command): void
    {
        $this->messageBus->dispatch($command);
    }
}
