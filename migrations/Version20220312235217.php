<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20220312235217 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Create the recipe table';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $sql = <<<SQL
CREATE TABLE IF NOT EXISTS recipe
(
    event_id          UUID NOT NULL PRIMARY KEY,
    aggregate_root_id UUID NOT NULL,
    version           INT  NULL,
    payload           TEXT NOT NULL
);
SQL;

        $this->addSql($sql);
        $this->addSql('CREATE INDEX aggregate_root_id ON recipe (aggregate_root_id);');
        $this->addSql('CREATE INDEX reconstitution ON recipe (aggregate_root_id, version ASC)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs

    }
}
