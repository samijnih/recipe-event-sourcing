<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20220320170211 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'It creates the outbox messages table.';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $sql = <<<SQL
CREATE TABLE IF NOT EXISTS public.outbox_messages
(
    id       BIGSERIAL NOT NULL PRIMARY KEY,
    consumed BOOLEAN   NOT NULL DEFAULT FALSE,
    payload  TEXT      NOT NULL
);
SQL;

        $this->addSql($sql);
        $this->addSql('CREATE INDEX is_consumed ON public.outbox_messages (consumed, id ASC);');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs

    }
}
