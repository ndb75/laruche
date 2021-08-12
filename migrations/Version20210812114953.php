<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20210812114953 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE gift (id INT AUTO_INCREMENT NOT NULL, receiver_id INT DEFAULT NULL, stock_id INT NOT NULL, code VARCHAR(255) NOT NULL, description VARCHAR(255) NOT NULL, price DOUBLE PRECISION NOT NULL, uuid VARCHAR(255) NOT NULL, UNIQUE INDEX UNIQ_A47C990DD17F50A6 (uuid), INDEX IDX_A47C990DCD53EDB6 (receiver_id), INDEX IDX_A47C990DDCD6110 (stock_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE receiver (id INT AUTO_INCREMENT NOT NULL, firstname VARCHAR(255) NOT NULL, lastname VARCHAR(255) NOT NULL, uuid VARCHAR(255) NOT NULL, UNIQUE INDEX UNIQ_3DB88C96D17F50A6 (uuid), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE stock (id INT AUTO_INCREMENT NOT NULL, filename VARCHAR(255) NOT NULL, date_upload DATETIME NOT NULL, average_price DOUBLE PRECISION DEFAULT NULL, max_price DOUBLE PRECISION DEFAULT NULL, min_price DOUBLE PRECISION DEFAULT NULL, nb_country INT DEFAULT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE user (id INT AUTO_INCREMENT NOT NULL, email VARCHAR(180) NOT NULL, roles JSON NOT NULL, password VARCHAR(255) NOT NULL, UNIQUE INDEX UNIQ_8D93D649E7927C74 (email), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE gift ADD CONSTRAINT FK_A47C990DCD53EDB6 FOREIGN KEY (receiver_id) REFERENCES receiver (id)');
        $this->addSql('ALTER TABLE gift ADD CONSTRAINT FK_A47C990DDCD6110 FOREIGN KEY (stock_id) REFERENCES stock (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE gift DROP FOREIGN KEY FK_A47C990DCD53EDB6');
        $this->addSql('ALTER TABLE gift DROP FOREIGN KEY FK_A47C990DDCD6110');
        $this->addSql('DROP TABLE gift');
        $this->addSql('DROP TABLE receiver');
        $this->addSql('DROP TABLE stock');
        $this->addSql('DROP TABLE user');
    }
}
