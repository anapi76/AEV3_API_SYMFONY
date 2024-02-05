<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240128202921 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE comandas (idComanda INT AUTO_INCREMENT NOT NULL, fecha DATETIME NOT NULL, comensales INT NOT NULL, detalles VARCHAR(250) DEFAULT NULL, estado TINYINT(1) DEFAULT 1 NOT NULL, idMesa INT NOT NULL, INDEX IDX_774D9FE8EB4EDC6C (idMesa), PRIMARY KEY(idComanda)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE lineascomandas (idlinea INT AUTO_INCREMENT NOT NULL, cantidad NUMERIC(8, 2) NOT NULL, entregado TINYINT(1) DEFAULT 0 NOT NULL, idComanda INT NOT NULL, idProducto INT NOT NULL, INDEX IDX_FB71E2C6557C65A6 (idComanda), INDEX IDX_FB71E2C6F4182C4E (idProducto), PRIMARY KEY(idlinea)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE lineaspedidos (idlinea INT AUTO_INCREMENT NOT NULL, cantidad NUMERIC(8, 2) NOT NULL, entregado TINYINT(1) DEFAULT 0 NOT NULL, idPedido INT NOT NULL, idProducto INT NOT NULL, INDEX IDX_881711F4A7FDBE54 (idPedido), INDEX IDX_881711F4F4182C4E (idProducto), PRIMARY KEY(idlinea)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE mesa (idMesa INT AUTO_INCREMENT NOT NULL, nombre VARCHAR(50) NOT NULL, comensales INT NOT NULL, UNIQUE INDEX UNIQ_98B382F23A909126 (nombre), PRIMARY KEY(idMesa)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE pedidos (idPedidos INT AUTO_INCREMENT NOT NULL, fecha DATETIME NOT NULL, detalles VARCHAR(100) DEFAULT NULL, estado TINYINT(1) DEFAULT 1 NOT NULL, idProveedor INT NOT NULL, INDEX IDX_6716CCAAEA2A4398 (idProveedor), PRIMARY KEY(idPedidos)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE productos (idProducto INT AUTO_INCREMENT NOT NULL, nombre VARCHAR(50) NOT NULL, descripcion VARCHAR(100) DEFAULT NULL, precio NUMERIC(8, 2) DEFAULT \'0\' NOT NULL, UNIQUE INDEX UNIQ_767490E63A909126 (nombre), PRIMARY KEY(idProducto)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE proveedores (idProveedor INT AUTO_INCREMENT NOT NULL, nombre VARCHAR(255) NOT NULL, cif VARCHAR(9) NOT NULL, direccion LONGTEXT NOT NULL, telefono INT DEFAULT NULL, email VARCHAR(100) DEFAULT NULL, contacto VARCHAR(100) DEFAULT NULL, UNIQUE INDEX UNIQ_864FA8F13A909126 (nombre), UNIQUE INDEX UNIQ_864FA8F1A53EB8E8 (cif), PRIMARY KEY(idProveedor)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE stock (id_producto INT NOT NULL, idStock INT AUTO_INCREMENT NOT NULL, fecha DATETIME NOT NULL, cantidad NUMERIC(8, 2) DEFAULT \'0\' NOT NULL, INDEX IDX_4B365660F760EA80 (id_producto), PRIMARY KEY(idStock)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE tickets (idTicket INT AUTO_INCREMENT NOT NULL, fecha DATETIME NOT NULL, importe NUMERIC(10, 2) NOT NULL, pagado TINYINT(1) DEFAULT 0 NOT NULL, idComanda INT NOT NULL, INDEX IDX_54469DF4557C65A6 (idComanda), PRIMARY KEY(idTicket)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE comandas ADD CONSTRAINT FK_774D9FE8EB4EDC6C FOREIGN KEY (idMesa) REFERENCES mesa (idMesa)');
        $this->addSql('ALTER TABLE lineascomandas ADD CONSTRAINT FK_FB71E2C6557C65A6 FOREIGN KEY (idComanda) REFERENCES comandas (idComanda)');
        $this->addSql('ALTER TABLE lineascomandas ADD CONSTRAINT FK_FB71E2C6F4182C4E FOREIGN KEY (idProducto) REFERENCES productos (idProducto)');
        $this->addSql('ALTER TABLE lineaspedidos ADD CONSTRAINT FK_881711F4A7FDBE54 FOREIGN KEY (idPedido) REFERENCES pedidos (idPedidos)');
        $this->addSql('ALTER TABLE lineaspedidos ADD CONSTRAINT FK_881711F4F4182C4E FOREIGN KEY (idProducto) REFERENCES productos (idProducto)');
        $this->addSql('ALTER TABLE pedidos ADD CONSTRAINT FK_6716CCAAEA2A4398 FOREIGN KEY (idProveedor) REFERENCES proveedores (idProveedor)');
        $this->addSql('ALTER TABLE stock ADD CONSTRAINT FK_4B365660F760EA80 FOREIGN KEY (id_producto) REFERENCES productos (idProducto)');
        $this->addSql('ALTER TABLE tickets ADD CONSTRAINT FK_54469DF4557C65A6 FOREIGN KEY (idComanda) REFERENCES comandas (idComanda)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE comandas DROP FOREIGN KEY FK_774D9FE8EB4EDC6C');
        $this->addSql('ALTER TABLE lineascomandas DROP FOREIGN KEY FK_FB71E2C6557C65A6');
        $this->addSql('ALTER TABLE lineascomandas DROP FOREIGN KEY FK_FB71E2C6F4182C4E');
        $this->addSql('ALTER TABLE lineaspedidos DROP FOREIGN KEY FK_881711F4A7FDBE54');
        $this->addSql('ALTER TABLE lineaspedidos DROP FOREIGN KEY FK_881711F4F4182C4E');
        $this->addSql('ALTER TABLE pedidos DROP FOREIGN KEY FK_6716CCAAEA2A4398');
        $this->addSql('ALTER TABLE stock DROP FOREIGN KEY FK_4B365660F760EA80');
        $this->addSql('ALTER TABLE tickets DROP FOREIGN KEY FK_54469DF4557C65A6');
        $this->addSql('DROP TABLE comandas');
        $this->addSql('DROP TABLE lineascomandas');
        $this->addSql('DROP TABLE lineaspedidos');
        $this->addSql('DROP TABLE mesa');
        $this->addSql('DROP TABLE pedidos');
        $this->addSql('DROP TABLE productos');
        $this->addSql('DROP TABLE proveedores');
        $this->addSql('DROP TABLE stock');
        $this->addSql('DROP TABLE tickets');
    }
}
