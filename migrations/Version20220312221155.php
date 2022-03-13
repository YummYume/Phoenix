<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20220312221155 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Adds dynamic row format to avoid index column size being too large errors.';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('SET GLOBAL innodb_default_row_format = \'DYNAMIC\';');
    }

    public function down(Schema $schema): void
    {
    }
}
