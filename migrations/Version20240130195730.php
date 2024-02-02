<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240130195730 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE pedidos DROP FOREIGN KEY FK_6716CCAAEA2A4398');
        $this->addSql('ALTER TABLE pedidos ADD CONSTRAINT FK_6716CCAAEA2A4398 FOREIGN KEY (idProveedor) REFERENCES proveedores (idProveedor) ON DELETE SET NULL');
        $this->addSql('ALTER TABLE productos CHANGE precio precio NUMERIC(8, 2) DEFAULT \'0\' NOT NULL');
        $this->addSql('ALTER TABLE stock CHANGE cantidad cantidad NUMERIC(8, 2) DEFAULT \'0\' NOT NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE pedidos DROP FOREIGN KEY FK_6716CCAAEA2A4398');
        $this->addSql('ALTER TABLE pedidos ADD CONSTRAINT FK_6716CCAAEA2A4398 FOREIGN KEY (idProveedor) REFERENCES proveedores (idProveedor)');
        $this->addSql('ALTER TABLE productos CHANGE precio precio NUMERIC(8, 2) DEFAULT \'0.00\' NOT NULL');
        $this->addSql('ALTER TABLE stock CHANGE cantidad cantidad NUMERIC(8, 2) DEFAULT \'0.00\' NOT NULL');
    }
}
