<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20201121104443 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE advance (id INT AUTO_INCREMENT NOT NULL, amount DOUBLE PRECISION NOT NULL, created_at DATETIME NOT NULL, content VARCHAR(255) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE company (id INT AUTO_INCREMENT NOT NULL, email VARCHAR(180) NOT NULL, roles JSON NOT NULL, password VARCHAR(255) NOT NULL, name VARCHAR(255) NOT NULL, firstname VARCHAR(255) NOT NULL, lastname VARCHAR(255) NOT NULL, address VARCHAR(255) NOT NULL, post_code VARCHAR(255) NOT NULL, city VARCHAR(255) NOT NULL, phone VARCHAR(255) NOT NULL, siret VARCHAR(255) NOT NULL, rcs VARCHAR(255) NOT NULL, mention1 VARCHAR(255) DEFAULT NULL, mention2 VARCHAR(255) DEFAULT NULL, mention3 VARCHAR(255) DEFAULT NULL, mention4 VARCHAR(255) DEFAULT NULL, mention5 VARCHAR(255) DEFAULT NULL, UNIQUE INDEX UNIQ_4FBF094FE7927C74 (email), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE customer (id INT AUTO_INCREMENT NOT NULL, firstname VARCHAR(255) NOT NULL, lastname VARCHAR(255) NOT NULL, address VARCHAR(255) NOT NULL, post_code VARCHAR(255) NOT NULL, city VARCHAR(255) NOT NULL, email VARCHAR(255) NOT NULL, phone VARCHAR(255) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE description (id INT AUTO_INCREMENT NOT NULL, prestation VARCHAR(255) NOT NULL, created_at DATETIME NOT NULL, unit_price DOUBLE PRECISION NOT NULL, quantity DOUBLE PRECISION NOT NULL, tva DOUBLE PRECISION NOT NULL, total_ht DOUBLE PRECISION NOT NULL, total_ttc DOUBLE PRECISION NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE estimate (id INT AUTO_INCREMENT NOT NULL, total_ht DOUBLE PRECISION NOT NULL, total_ttc DOUBLE PRECISION NOT NULL, total_advance DOUBLE PRECISION NOT NULL, state TINYINT(1) NOT NULL, created_at DATETIME NOT NULL, reference VARCHAR(255) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE invoice (id INT AUTO_INCREMENT NOT NULL, total_ht DOUBLE PRECISION NOT NULL, total_ttc DOUBLE PRECISION NOT NULL, total_advance DOUBLE PRECISION NOT NULL, state TINYINT(1) NOT NULL, type_invoice VARCHAR(255) NOT NULL, created_at DATETIME NOT NULL, reference VARCHAR(255) NOT NULL, remaining_capital DOUBLE PRECISION NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP TABLE advance');
        $this->addSql('DROP TABLE company');
        $this->addSql('DROP TABLE customer');
        $this->addSql('DROP TABLE description');
        $this->addSql('DROP TABLE estimate');
        $this->addSql('DROP TABLE invoice');
    }
}
