<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240426100310 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE association (id INT AUTO_INCREMENT NOT NULL, president_id INT NOT NULL, referent_id INT DEFAULT NULL, code INT NOT NULL, libelle VARCHAR(255) NOT NULL, adress LONGTEXT NOT NULL, cp VARCHAR(255) NOT NULL, city VARCHAR(255) NOT NULL, tel VARCHAR(255) DEFAULT NULL, email VARCHAR(255) NOT NULL, UNIQUE INDEX UNIQ_FD8521CCB40A33C7 (president_id), UNIQUE INDEX UNIQ_FD8521CC35E47E35 (referent_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE campain_association (id INT AUTO_INCREMENT NOT NULL, campains_id INT NOT NULL, association_id INT NOT NULL, statut VARCHAR(255) NOT NULL, send_at DATETIME DEFAULT NULL, emails VARCHAR(255) DEFAULT NULL, texte_personnalise LONGTEXT DEFAULT NULL, updated_text_at DATETIME DEFAULT NULL COMMENT \'(DC2Type:datetime_immutable)\', token VARCHAR(255) DEFAULT NULL, INDEX IDX_51C0819F595A1A47 (campains_id), INDEX IDX_51C0819FEFB9C8A5 (association_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE campains (id INT AUTO_INCREMENT NOT NULL, oldcampain_id INT DEFAULT NULL, libelle VARCHAR(255) NOT NULL, date DATE NOT NULL, date_send DATE DEFAULT NULL, objet_email VARCHAR(255) DEFAULT NULL, texte_email LONGTEXT DEFAULT NULL, destinataire TINYTEXT DEFAULT NULL COMMENT \'(DC2Type:array)\', email_from VARCHAR(255) DEFAULT NULL, email_cc VARCHAR(255) DEFAULT NULL, valid TINYINT(1) NOT NULL, UNIQUE INDEX UNIQ_ADD1C54D289F97DE (oldcampain_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE history (id INT AUTO_INCREMENT NOT NULL, association_id INT NOT NULL, table_name VARCHAR(255) NOT NULL, row_id INT NOT NULL, start_date DATE NOT NULL, end_date DATE DEFAULT NULL, INDEX IDX_27BA704BEFB9C8A5 (association_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE logs (id INT AUTO_INCREMENT NOT NULL, user_id INT NOT NULL, table_name VARCHAR(255) NOT NULL, row_id INT NOT NULL, action VARCHAR(255) NOT NULL, date DATETIME NOT NULL, old_data LONGTEXT DEFAULT NULL, new_data LONGTEXT DEFAULT NULL, INDEX IDX_F08FC65CA76ED395 (user_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE president (id INT AUTO_INCREMENT NOT NULL, user_id INT NOT NULL, fonction VARCHAR(255) NOT NULL, UNIQUE INDEX UNIQ_6E8BD214A76ED395 (user_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE referent (id INT AUTO_INCREMENT NOT NULL, user_id INT NOT NULL, tel VARCHAR(255) DEFAULT NULL, UNIQUE INDEX UNIQ_FE9AAC6CA76ED395 (user_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE user (id INT AUTO_INCREMENT NOT NULL, email VARCHAR(180) NOT NULL, roles JSON NOT NULL, password VARCHAR(255) NOT NULL, firstname VARCHAR(60) NOT NULL, lastname VARCHAR(60) NOT NULL, last_login_at DATETIME DEFAULT NULL COMMENT \'(DC2Type:datetime_immutable)\', president VARCHAR(255) DEFAULT NULL, referent VARCHAR(255) DEFAULT NULL, UNIQUE INDEX UNIQ_IDENTIFIER_EMAIL (email), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE messenger_messages (id BIGINT AUTO_INCREMENT NOT NULL, body LONGTEXT NOT NULL, headers LONGTEXT NOT NULL, queue_name VARCHAR(190) NOT NULL, created_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', available_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', delivered_at DATETIME DEFAULT NULL COMMENT \'(DC2Type:datetime_immutable)\', INDEX IDX_75EA56E0FB7336F0 (queue_name), INDEX IDX_75EA56E0E3BD61CE (available_at), INDEX IDX_75EA56E016BA31DB (delivered_at), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE association ADD CONSTRAINT FK_FD8521CCB40A33C7 FOREIGN KEY (president_id) REFERENCES president (id)');
        $this->addSql('ALTER TABLE association ADD CONSTRAINT FK_FD8521CC35E47E35 FOREIGN KEY (referent_id) REFERENCES referent (id)');
        $this->addSql('ALTER TABLE campain_association ADD CONSTRAINT FK_51C0819F595A1A47 FOREIGN KEY (campains_id) REFERENCES campains (id)');
        $this->addSql('ALTER TABLE campain_association ADD CONSTRAINT FK_51C0819FEFB9C8A5 FOREIGN KEY (association_id) REFERENCES association (id)');
        $this->addSql('ALTER TABLE campains ADD CONSTRAINT FK_ADD1C54D289F97DE FOREIGN KEY (oldcampain_id) REFERENCES campains (id)');
        $this->addSql('ALTER TABLE history ADD CONSTRAINT FK_27BA704BEFB9C8A5 FOREIGN KEY (association_id) REFERENCES association (id)');
        $this->addSql('ALTER TABLE logs ADD CONSTRAINT FK_F08FC65CA76ED395 FOREIGN KEY (user_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE president ADD CONSTRAINT FK_6E8BD214A76ED395 FOREIGN KEY (user_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE referent ADD CONSTRAINT FK_FE9AAC6CA76ED395 FOREIGN KEY (user_id) REFERENCES user (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE association DROP FOREIGN KEY FK_FD8521CCB40A33C7');
        $this->addSql('ALTER TABLE association DROP FOREIGN KEY FK_FD8521CC35E47E35');
        $this->addSql('ALTER TABLE campain_association DROP FOREIGN KEY FK_51C0819F595A1A47');
        $this->addSql('ALTER TABLE campain_association DROP FOREIGN KEY FK_51C0819FEFB9C8A5');
        $this->addSql('ALTER TABLE campains DROP FOREIGN KEY FK_ADD1C54D289F97DE');
        $this->addSql('ALTER TABLE history DROP FOREIGN KEY FK_27BA704BEFB9C8A5');
        $this->addSql('ALTER TABLE logs DROP FOREIGN KEY FK_F08FC65CA76ED395');
        $this->addSql('ALTER TABLE president DROP FOREIGN KEY FK_6E8BD214A76ED395');
        $this->addSql('ALTER TABLE referent DROP FOREIGN KEY FK_FE9AAC6CA76ED395');
        $this->addSql('DROP TABLE association');
        $this->addSql('DROP TABLE campain_association');
        $this->addSql('DROP TABLE campains');
        $this->addSql('DROP TABLE history');
        $this->addSql('DROP TABLE logs');
        $this->addSql('DROP TABLE president');
        $this->addSql('DROP TABLE referent');
        $this->addSql('DROP TABLE user');
        $this->addSql('DROP TABLE messenger_messages');
    }
}
