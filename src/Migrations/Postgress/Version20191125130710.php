<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20191125130710 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'postgresql', 'Migration can only be executed safely on \'postgresql\'.');

        $this->addSql('ALTER SEQUENCE shipping_address_id_seq INCREMENT BY 1');
        $this->addSql('ALTER SEQUENCE my_user_id_seq INCREMENT BY 1');
        $this->addSql('CREATE UNIQUE INDEX default_uniq_index ON shipping_address (user_id, default_address) WHERE (default_address != \'f\')');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'postgresql', 'Migration can only be executed safely on \'postgresql\'.');

        $this->addSql('CREATE SCHEMA public');
        $this->addSql('ALTER SEQUENCE shipping_address_id_seq INCREMENT BY 30');
        $this->addSql('ALTER SEQUENCE my_user_id_seq INCREMENT BY 32');
        $this->addSql('DROP INDEX default_uniq_index');
    }
}
