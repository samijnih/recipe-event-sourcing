<?php

declare(strict_types=1);

namespace Recipe\Tests\Acceptance\InMemory\Recipe;

use Doctrine\DBAL\Connection;
use EventSauce\EventSourcing\AggregateRootId;
use EventSauce\EventSourcing\AggregateRootRepository;
use EventSauce\EventSourcing\DotSeparatedSnakeCaseInflector;
use EventSauce\EventSourcing\MessageDecorator;
use EventSauce\EventSourcing\MessageDispatcher;
use EventSauce\EventSourcing\MessageRepository;
use EventSauce\EventSourcing\TestUtilities\AggregateRootTestCase;
use EventSauce\MessageOutbox\DoctrineOutbox\DoctrineTransactionalMessageRepository;
use EventSauce\MessageOutbox\InMemoryOutboxRepository;
use EventSauce\MessageOutbox\OutboxRepository;
use PHPUnit\Framework\MockObject\MockObject;
use Ramsey\Uuid\UuidFactoryInterface;
use Recipe\Domain\Bus\Command;
use Recipe\Domain\Model\Recipe\Recipe;
use Recipe\Domain\Model\Recipe\RecipeRepository;
use Recipe\Domain\Model\Recipe\RecipeUuid;
use Recipe\Domain\UseCase\Recipe\Create\CreateARecipe;
use Recipe\Domain\UseCase\Recipe\Create\HandleCreateARecipe;
use Recipe\Infrastructure\Persistence\Repository\DbalRecipeRepository;
use Recipe\Tests\Domain\Bus\InMemoryCommandBus;

abstract class RecipeTest extends AggregateRootTestCase
{
    protected AggregateRootRepository|RecipeRepository $repository;
    protected MockObject|UuidFactoryInterface $uuidFactory;

    private static OutboxRepository $outboxRepository;
    private ?int $expectedPendingMessagesCount = null;

    public static function setUpBeforeClass(): void
    {
        self::$outboxRepository = new InMemoryOutboxRepository();
    }

    /** {@inheritDoc} */
    protected function aggregateRootRepository(
        string $className,
        MessageRepository $repository,
        MessageDispatcher $dispatcher,
        MessageDecorator $decorator
    ): AggregateRootRepository {
        return new DbalRecipeRepository(
            $className,
            new DoctrineTransactionalMessageRepository(
                $this->createStub(Connection::class),
                $repository,
                self::$outboxRepository,
            ),
            $decorator,
            new DotSeparatedSnakeCaseInflector(),
        );
    }

    protected function setUp(): void
    {
        $this->uuidFactory = $this->createMock(UuidFactoryInterface::class);
        $this->messageBus = new InMemoryCommandBus([
            CreateARecipe::class => new HandleCreateARecipe(
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

    protected function withPendingMessagesCount(int $count): self
    {
        $this->expectedPendingMessagesCount = $count;

        return $this;
    }

    /**
     * @after
     */
    protected function assertScenario(): void
    {
        parent::assertScenario();

        if ($this->expectedPendingMessagesCount) {
            $actual = self::$outboxRepository->numberOfPendingMessages();
            self::assertSame(
                $this->expectedPendingMessagesCount,
                $actual,
                sprintf(
                    'Expected %1$d message%3$s from outbox repository. Got %2$d message%4$s.',
                    $this->expectedPendingMessagesCount,
                    $actual,
                    $this->expectedPendingMessagesCount > 1 ? 's' : '',
                    $actual > 1 ? 's' : '',
                ),
            );
        }
    }
}
