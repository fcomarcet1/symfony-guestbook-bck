<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20210920071115 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'creates `state` field in `comment` table';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE comment ADD state VARCHAR(255) NOT NULL');
        $this->addSql("UPDATE comment SET state='published'");
        $this->addSql('ALTER TABLE comment ALTER COLUMN state SET NOT NULL');

    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('ALTER TABLE comment DROP state');
    }
}
