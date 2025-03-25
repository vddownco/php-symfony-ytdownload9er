<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20250325040347 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Update table souce, remove softdelete';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('ALTER TABLE source DROP deleted_at');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('ALTER TABLE source ADD deleted_at DATETIME DEFAULT NULL');
    }
}
