<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20201121105213 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE advance ADD invoice_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE advance ADD CONSTRAINT FK_E7811BF32989F1FD FOREIGN KEY (invoice_id) REFERENCES invoice (id)');
        $this->addSql('CREATE INDEX IDX_E7811BF32989F1FD ON advance (invoice_id)');
        $this->addSql('ALTER TABLE customer ADD company_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE customer ADD CONSTRAINT FK_81398E09979B1AD6 FOREIGN KEY (company_id) REFERENCES company (id)');
        $this->addSql('CREATE INDEX IDX_81398E09979B1AD6 ON customer (company_id)');
        $this->addSql('ALTER TABLE description ADD estimate_id INT DEFAULT NULL, ADD invoice_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE description ADD CONSTRAINT FK_6DE4402685F23082 FOREIGN KEY (estimate_id) REFERENCES estimate (id)');
        $this->addSql('ALTER TABLE description ADD CONSTRAINT FK_6DE440262989F1FD FOREIGN KEY (invoice_id) REFERENCES invoice (id)');
        $this->addSql('CREATE INDEX IDX_6DE4402685F23082 ON description (estimate_id)');
        $this->addSql('CREATE INDEX IDX_6DE440262989F1FD ON description (invoice_id)');
        $this->addSql('ALTER TABLE estimate ADD customer_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE estimate ADD CONSTRAINT FK_D2EA46079395C3F3 FOREIGN KEY (customer_id) REFERENCES customer (id)');
        $this->addSql('CREATE INDEX IDX_D2EA46079395C3F3 ON estimate (customer_id)');
        $this->addSql('ALTER TABLE invoice ADD customer_id INT DEFAULT NULL, ADD estimate_id INT NOT NULL');
        $this->addSql('ALTER TABLE invoice ADD CONSTRAINT FK_906517449395C3F3 FOREIGN KEY (customer_id) REFERENCES customer (id)');
        $this->addSql('ALTER TABLE invoice ADD CONSTRAINT FK_9065174485F23082 FOREIGN KEY (estimate_id) REFERENCES estimate (id)');
        $this->addSql('CREATE INDEX IDX_906517449395C3F3 ON invoice (customer_id)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_9065174485F23082 ON invoice (estimate_id)');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE advance DROP FOREIGN KEY FK_E7811BF32989F1FD');
        $this->addSql('DROP INDEX IDX_E7811BF32989F1FD ON advance');
        $this->addSql('ALTER TABLE advance DROP invoice_id');
        $this->addSql('ALTER TABLE customer DROP FOREIGN KEY FK_81398E09979B1AD6');
        $this->addSql('DROP INDEX IDX_81398E09979B1AD6 ON customer');
        $this->addSql('ALTER TABLE customer DROP company_id');
        $this->addSql('ALTER TABLE description DROP FOREIGN KEY FK_6DE4402685F23082');
        $this->addSql('ALTER TABLE description DROP FOREIGN KEY FK_6DE440262989F1FD');
        $this->addSql('DROP INDEX IDX_6DE4402685F23082 ON description');
        $this->addSql('DROP INDEX IDX_6DE440262989F1FD ON description');
        $this->addSql('ALTER TABLE description DROP estimate_id, DROP invoice_id');
        $this->addSql('ALTER TABLE estimate DROP FOREIGN KEY FK_D2EA46079395C3F3');
        $this->addSql('DROP INDEX IDX_D2EA46079395C3F3 ON estimate');
        $this->addSql('ALTER TABLE estimate DROP customer_id');
        $this->addSql('ALTER TABLE invoice DROP FOREIGN KEY FK_906517449395C3F3');
        $this->addSql('ALTER TABLE invoice DROP FOREIGN KEY FK_9065174485F23082');
        $this->addSql('DROP INDEX IDX_906517449395C3F3 ON invoice');
        $this->addSql('DROP INDEX UNIQ_9065174485F23082 ON invoice');
        $this->addSql('ALTER TABLE invoice DROP customer_id, DROP estimate_id');
    }
}
