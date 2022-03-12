<?php

declare(strict_types=1);

namespace Recipe\Infrastructure\Persistence\Repository;

use Doctrine\DBAL\Connection;
use EventSauce\EventSourcing\MessageDispatcher;
use EventSauce\EventSourcing\MessageRepository;
use EventSauce\EventSourcing\Serialization\MessageSerializer;
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
        string $tableName,
        Connection $connection,
        MessageSerializer $messageSerializer,
        MessageDispatcher $messageDispatcher,
    ): RecipeRepository {
        return new DbalRecipeRepository(
            Recipe::class,
            $this->createMessageRepository($connection, $messageSerializer, $tableName),
            $messageDispatcher,
        );
    }

    private function createMessageRepository(
        Connection $connection,
        MessageSerializer $messageSerializer,
        string $tableName
    ): MessageRepository {
        return new DoctrineUuidV4MessageRepository(
            $connection,
            $tableName,
            $messageSerializer,
            0,
            null,
            $this->uuidEncoder,
        );
    }
}
