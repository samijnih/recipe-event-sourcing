<?php

declare(strict_types=1);

namespace Recipe\Infrastructure\Persistence\Repository;

use Doctrine\DBAL\Connection;
use EventSauce\EventSourcing\ClassNameInflector;
use EventSauce\EventSourcing\MessageDecorator;
use EventSauce\EventSourcing\Serialization\MessageSerializer;
use EventSauce\MessageOutbox\DoctrineOutbox\DoctrineTransactionalMessageRepository;
use EventSauce\MessageRepository\DoctrineMessageRepository\DoctrineUuidV4MessageRepository;
use EventSauce\UuidEncoding\StringUuidEncoder;
use EventSauce\UuidEncoding\UuidEncoder;
use Recipe\Domain\Model\Recipe\Recipe;
use Recipe\Domain\Model\Recipe\RecipeRepository;

final class RepositoryFactory
{
    private UuidEncoder $uuidEncoder;

    public function __construct()
    {
        $this->uuidEncoder = new StringUuidEncoder();
    }

    public function createRecipeRepository(
        string $aggregateTableName,
        string $outboxTableName,
        Connection $connection,
        MessageSerializer $messageSerializer,
        MessageDecorator $messageDecorator,
        ClassNameInflector $classNameInflector,
    ): RecipeRepository {
        return new DbalRecipeRepository(
            Recipe::class,
            new DoctrineTransactionalMessageRepository(
                $connection,
                new DoctrineUuidV4MessageRepository(
                    $connection,
                    $aggregateTableName,
                    $messageSerializer,
                    0,
                    null,
                    $this->uuidEncoder,
                ),
                new DbalOutboxRepository(
                    $connection,
                    $outboxTableName,
                    $messageSerializer
                ),
            ),
            $messageDecorator,
            $classNameInflector,
        );
    }
}
