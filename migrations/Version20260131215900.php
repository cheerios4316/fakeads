<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20260131215900 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TEMPORARY TABLE __temp__content AS SELECT id, url, file_name, clickout, size, description FROM content');
        $this->addSql('DROP TABLE content');
        $this->addSql('CREATE TABLE content (id BLOB NOT NULL, url VARCHAR(255) NOT NULL, file_name VARCHAR(255) NOT NULL, clickout VARCHAR(255) DEFAULT NULL, size VARCHAR(10) NOT NULL, description VARCHAR(255) DEFAULT NULL, PRIMARY KEY (id))');
        $this->addSql('INSERT INTO content (id, url, file_name, clickout, size, description) SELECT id, url, file_name, clickout, size, description FROM __temp__content');
        $this->addSql('DROP TABLE __temp__content');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TEMPORARY TABLE __temp__content AS SELECT id, url, file_name, description, clickout, size FROM content');
        $this->addSql('DROP TABLE content');
        $this->addSql('CREATE TABLE content (id BLOB NOT NULL, url VARCHAR(255) NOT NULL, file_name VARCHAR(255) NOT NULL, description VARCHAR(255) NOT NULL, clickout VARCHAR(255) DEFAULT NULL, size VARCHAR(10) NOT NULL, PRIMARY KEY (id))');
        $this->addSql('INSERT INTO content (id, url, file_name, description, clickout, size) SELECT id, url, file_name, description, clickout, size FROM __temp__content');
        $this->addSql('DROP TABLE __temp__content');
    }
}
