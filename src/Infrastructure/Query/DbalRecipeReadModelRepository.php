<?php

declare(strict_types=1);

namespace Recipe\Infrastructure\Query;

use Doctrine\DBAL\Connection;
use Recipe\Domain\ReadModel\Repository\RecipeReadModelRepository;
use Recipe\Domain\UseCase\Recipe\Get\GetARecipeModel;
use RuntimeException;

final class DbalRecipeReadModelRepository implements RecipeReadModelRepository
{
    private const PROJECTION_TABLE = 'public.get_a_recipe';

    public function __construct(private Connection $connection)
    {
    }

    public function getARecipe(string $id): GetARecipeModel
    {
        $table = self::PROJECTION_TABLE;

        $sql = <<<SQL
            SELECT id, name
            FROM $table
            WHERE id = :id
            SQL;

        $result = $this->connection->fetchAssociative($sql, ['id' => $id]);

        if (false === $result) {
            throw new RuntimeException();
        }

        return new GetARecipeModel(
            $result['id'],
            $result['name'],
        );
    }
}
