<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20220319163752 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'It creates the projection table for getting a recipe.';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $sql = <<<'SQL'
CREATE TABLE get_a_recipe
(
    id         UUID PRIMARY KEY NOT NULL,
    name       VARCHAR          NOT NULL,
    created_at timestamptz      NOT NULL DEFAULT CURRENT_TIMESTAMP
);
SQL;

        $this->addSql($sql);
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs

    }
}
