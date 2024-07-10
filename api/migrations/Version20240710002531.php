<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240710002531 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE book ADD slug VARCHAR(255)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_CBE5A331989D9B62 ON book (slug)');
        $this->addSql('UPDATE book SET slug = CONCAT(\'book-\', id::text)');
        $this->addSql('ALTER TABLE book ALTER COLUMN slug SET NOT NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('DROP INDEX UNIQ_CBE5A331989D9B62');
        $this->addSql('ALTER TABLE book DROP slug');
    }
}
