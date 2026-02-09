<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20260209120138 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE review (id BINARY(16) NOT NULL, content LONGTEXT DEFAULT NULL, stars INT DEFAULT NULL, product_id INT NOT NULL, sentiment VARCHAR(25) NOT NULL, primary_problem VARCHAR(25) DEFAULT NULL, primary_language VARCHAR(5) DEFAULT NULL, PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci`');
        $this->addSql('CREATE TABLE translation (object_type VARCHAR(255) NOT NULL, object_id VARCHAR(255) NOT NULL, locale VARCHAR(255) NOT NULL, field VARCHAR(255) NOT NULL, value LONGTEXT NOT NULL, id INT AUTO_INCREMENT NOT NULL, PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci`');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP TABLE review');
        $this->addSql('DROP TABLE translation');
    }
}
