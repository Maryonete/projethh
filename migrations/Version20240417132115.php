<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240417132115 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE association (id INT AUTO_INCREMENT NOT NULL, code INT NOT NULL, libelle VARCHAR(255) NOT NULL, adress LONGTEXT NOT NULL, cp VARCHAR(255) NOT NULL, city VARCHAR(255) NOT NULL, tel VARCHAR(255) DEFAULT NULL, email VARCHAR(255) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE campain_association (id INT AUTO_INCREMENT NOT NULL, campains_id INT NOT NULL, association_id INT NOT NULL, statut VARCHAR(255) NOT NULL, date_send DATE NOT NULL, objet_email VARCHAR(255) NOT NULL, texte_email LONGTEXT NOT NULL, destinataire VARCHAR(255) NOT NULL, texte_personnalise LONGTEXT DEFAULT NULL, updated_text_at DATETIME DEFAULT NULL COMMENT \'(DC2Type:datetime_immutable)\', INDEX IDX_51C0819F595A1A47 (campains_id), INDEX IDX_51C0819FEFB9C8A5 (association_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE campains (id INT AUTO_INCREMENT NOT NULL, libelle VARCHAR(255) NOT NULL, date DATETIME NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE file (id INT AUTO_INCREMENT NOT NULL, filename VARCHAR(255) NOT NULL, name VARCHAR(255) NOT NULL, created_at DATETIME DEFAULT NULL, import_at DATETIME DEFAULT NULL, validate TINYINT(1) NOT NULL, validate_at DATETIME DEFAULT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE logs (id INT AUTO_INCREMENT NOT NULL, user_id INT NOT NULL, table_name VARCHAR(255) NOT NULL, row_id INT NOT NULL, action VARCHAR(255) NOT NULL, date DATETIME NOT NULL, old_data LONGTEXT DEFAULT NULL, new_data LONGTEXT DEFAULT NULL, INDEX IDX_F08FC65CA76ED395 (user_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE person (id INT AUTO_INCREMENT NOT NULL, name_and_function VARCHAR(255) NOT NULL, signature_img VARCHAR(255) DEFAULT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE president (id INT AUTO_INCREMENT NOT NULL, user_id INT NOT NULL, fonction VARCHAR(255) NOT NULL, actif TINYINT(1) NOT NULL, INDEX IDX_6E8BD214A76ED395 (user_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE referent (id INT AUTO_INCREMENT NOT NULL, user_id INT NOT NULL, tel VARCHAR(255) DEFAULT NULL, actif TINYINT(1) NOT NULL, INDEX IDX_FE9AAC6CA76ED395 (user_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE user (id INT AUTO_INCREMENT NOT NULL, email VARCHAR(180) NOT NULL, roles JSON NOT NULL, password VARCHAR(255) NOT NULL, firstname VARCHAR(255) NOT NULL, lastname VARCHAR(255) NOT NULL, last_login_at DATETIME DEFAULT NULL COMMENT \'(DC2Type:datetime_immutable)\', UNIQUE INDEX UNIQ_IDENTIFIER_EMAIL (email), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE messenger_messages (id BIGINT AUTO_INCREMENT NOT NULL, body LONGTEXT NOT NULL, headers LONGTEXT NOT NULL, queue_name VARCHAR(190) NOT NULL, created_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', available_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', delivered_at DATETIME DEFAULT NULL COMMENT \'(DC2Type:datetime_immutable)\', INDEX IDX_75EA56E0FB7336F0 (queue_name), INDEX IDX_75EA56E0E3BD61CE (available_at), INDEX IDX_75EA56E016BA31DB (delivered_at), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE campain_association ADD CONSTRAINT FK_51C0819F595A1A47 FOREIGN KEY (campains_id) REFERENCES campains (id)');
        $this->addSql('ALTER TABLE campain_association ADD CONSTRAINT FK_51C0819FEFB9C8A5 FOREIGN KEY (association_id) REFERENCES association (id)');
        $this->addSql('ALTER TABLE logs ADD CONSTRAINT FK_F08FC65CA76ED395 FOREIGN KEY (user_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE president ADD CONSTRAINT FK_6E8BD214A76ED395 FOREIGN KEY (user_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE referent ADD CONSTRAINT FK_FE9AAC6CA76ED395 FOREIGN KEY (user_id) REFERENCES user (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE campain_association DROP FOREIGN KEY FK_51C0819F595A1A47');
        $this->addSql('ALTER TABLE campain_association DROP FOREIGN KEY FK_51C0819FEFB9C8A5');
        $this->addSql('ALTER TABLE logs DROP FOREIGN KEY FK_F08FC65CA76ED395');
        $this->addSql('ALTER TABLE president DROP FOREIGN KEY FK_6E8BD214A76ED395');
        $this->addSql('ALTER TABLE referent DROP FOREIGN KEY FK_FE9AAC6CA76ED395');
        $this->addSql('DROP TABLE association');
        $this->addSql('DROP TABLE campain_association');
        $this->addSql('DROP TABLE campains');
        $this->addSql('DROP TABLE file');
        $this->addSql('DROP TABLE logs');
        $this->addSql('DROP TABLE person');
        $this->addSql('DROP TABLE president');
        $this->addSql('DROP TABLE referent');
        $this->addSql('DROP TABLE user');
        $this->addSql('DROP TABLE messenger_messages');
    }
}
