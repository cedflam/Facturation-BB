<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20201123085811 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE advance ADD means_payment VARCHAR(255) NOT NULL');
        $this->addSql('ALTER TABLE description DROP FOREIGN KEY FK_6DE440262989F1FD');
        $this->addSql('ALTER TABLE description DROP FOREIGN KEY FK_6DE4402685F23082');
        $this->addSql('ALTER TABLE description ADD CONSTRAINT FK_6DE440262989F1FD FOREIGN KEY (invoice_id) REFERENCES invoice (id)');
        $this->addSql('ALTER TABLE description ADD CONSTRAINT FK_6DE4402685F23082 FOREIGN KEY (estimate_id) REFERENCES estimate (id)');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE advance DROP means_payment');
        $this->addSql('ALTER TABLE description DROP FOREIGN KEY FK_6DE4402685F23082');
        $this->addSql('ALTER TABLE description DROP FOREIGN KEY FK_6DE440262989F1FD');
        $this->addSql('ALTER TABLE description ADD CONSTRAINT FK_6DE4402685F23082 FOREIGN KEY (estimate_id) REFERENCES estimate (id) ON UPDATE CASCADE ON DELETE CASCADE');
        $this->addSql('ALTER TABLE description ADD CONSTRAINT FK_6DE440262989F1FD FOREIGN KEY (invoice_id) REFERENCES invoice (id) ON UPDATE CASCADE ON DELETE CASCADE');
    }
}
