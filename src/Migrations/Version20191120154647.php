<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20191120154647 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    /**
     * @param Schema $schema
     * @throws \Doctrine\DBAL\Schema\SchemaException
     */
    public function up(Schema $schema): void
    {
        $this->skipIf(
            !$schema->hasTable('shipping_address'),
            'Table shipping_address doesn\'t exist'
        );
        $table = $schema->getTable('shipping_address');
        $columns = [];
        if (!$table->hasColumn('created_at')) {
            $columns[] = 'ADD `created_at` DATETIME NOT NULL';
        }

        if (!$table->hasColumn('updated_at')) {
            $columns[] = 'ADD `updated_at` DATETIME NOT NULL';
        }

        if (count($columns)) {
            $query = sprintf('ALTER TABLE `shipping_address` %s', implode(', ', $columns));
            $this->addSql($query);
        }
    }

    /**
     * @param Schema $schema
     * @throws \Doctrine\DBAL\Schema\SchemaException
     */
    public function down(Schema $schema): void
    {
        $this->skipIf(
            !$schema->hasTable('shipping_address'),
            'Table shipping_address doesn\'t exist'
        );
        $table = $schema->getTable('shipping_address');
        $columns = [];
        if ($table->hasColumn('created_at')) {
            $columns[] = 'DROP `created_at`';
        }

        if ($table->hasColumn('updated_at')) {
            $columns[] = 'DROP `updated_at`';
        }

        if (count($columns)) {
            $query = sprintf('ALTER TABLE `shipping_address` %s', implode(', ', $columns));
            $this->addSql($query);
        }
    }
}
