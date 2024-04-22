<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240421171554 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE association CHANGE tel tel VARCHAR(255) DEFAULT NULL');
        $this->addSql('ALTER TABLE campain_association CHANGE updated_text_at updated_text_at DATETIME DEFAULT NULL COMMENT \'(DC2Type:datetime_immutable)\', CHANGE emails emails VARCHAR(255) DEFAULT NULL, CHANGE send_at send_at DATETIME DEFAULT NULL');
        $this->addSql('ALTER TABLE campains ADD oldcampain_id INT DEFAULT NULL, CHANGE date_send date_send DATE DEFAULT NULL, CHANGE objet_email objet_email VARCHAR(255) DEFAULT NULL, CHANGE destinataire destinataire TINYTEXT DEFAULT NULL COMMENT \'(DC2Type:array)\', CHANGE email_from email_from VARCHAR(255) DEFAULT NULL, CHANGE email_cc email_cc VARCHAR(255) DEFAULT NULL');
        $this->addSql('ALTER TABLE campains ADD CONSTRAINT FK_ADD1C54D289F97DE FOREIGN KEY (oldcampain_id) REFERENCES campains (id)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_ADD1C54D289F97DE ON campains (oldcampain_id)');
        $this->addSql('ALTER TABLE history CHANGE end_date end_date DATE DEFAULT NULL');
        $this->addSql('ALTER TABLE referent CHANGE tel tel VARCHAR(255) DEFAULT NULL');
        $this->addSql('ALTER TABLE user CHANGE roles roles JSON NOT NULL, CHANGE last_login_at last_login_at DATETIME DEFAULT NULL COMMENT \'(DC2Type:datetime_immutable)\', CHANGE president president VARCHAR(255) DEFAULT NULL, CHANGE referent referent VARCHAR(255) DEFAULT NULL');
        $this->addSql('ALTER TABLE messenger_messages CHANGE delivered_at delivered_at DATETIME DEFAULT NULL COMMENT \'(DC2Type:datetime_immutable)\'');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE association CHANGE tel tel VARCHAR(255) DEFAULT \'NULL\'');
        $this->addSql('ALTER TABLE campains DROP FOREIGN KEY FK_ADD1C54D289F97DE');
        $this->addSql('DROP INDEX UNIQ_ADD1C54D289F97DE ON campains');
        $this->addSql('ALTER TABLE campains DROP oldcampain_id, CHANGE date_send date_send DATE DEFAULT \'NULL\', CHANGE objet_email objet_email VARCHAR(255) DEFAULT \'NULL\', CHANGE destinataire destinataire TINYTEXT DEFAULT \'NULL\' COMMENT \'(DC2Type:array)\', CHANGE email_from email_from VARCHAR(255) DEFAULT \'NULL\', CHANGE email_cc email_cc VARCHAR(255) DEFAULT \'NULL\'');
        $this->addSql('ALTER TABLE campain_association CHANGE send_at send_at DATETIME DEFAULT \'NULL\', CHANGE emails emails VARCHAR(255) DEFAULT \'NULL\', CHANGE updated_text_at updated_text_at DATETIME DEFAULT \'NULL\' COMMENT \'(DC2Type:datetime_immutable)\'');
        $this->addSql('ALTER TABLE history CHANGE end_date end_date DATE DEFAULT \'NULL\'');
        $this->addSql('ALTER TABLE messenger_messages CHANGE delivered_at delivered_at DATETIME DEFAULT \'NULL\' COMMENT \'(DC2Type:datetime_immutable)\'');
        $this->addSql('ALTER TABLE referent CHANGE tel tel VARCHAR(255) DEFAULT \'NULL\'');
        $this->addSql('ALTER TABLE user CHANGE roles roles LONGTEXT NOT NULL COLLATE `utf8mb4_bin`, CHANGE last_login_at last_login_at DATETIME DEFAULT \'NULL\' COMMENT \'(DC2Type:datetime_immutable)\', CHANGE president president VARCHAR(255) DEFAULT \'NULL\', CHANGE referent referent VARCHAR(255) DEFAULT \'NULL\'');
    }
}
