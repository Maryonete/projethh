<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class VersionYYYYMMDDHHMMSS extends AbstractMigration
{
    public function up(Schema $schema): void
    {
        $this->addSql("INSERT INTO `user` (`id`, `email`, `roles`, `password`, `firstname`, `lastname`) 
        VALUES (NULL, 'test@test.fr', '[\"ROLE_ADMIN\"]', 
        '$2y$13\$pZUgmzngb4f3dT2K6q3yde9x5Uszwz01Lfv9UoKAJ5qmrWRTQhd8i', 'Admin', 'HH')");
    }

    public function down(Schema $schema): void
    {
        // Vous pouvez ajouter du code pour annuler la migration si nécessaire
    }
}
