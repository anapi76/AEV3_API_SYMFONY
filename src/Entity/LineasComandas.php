<?php

namespace App\Entity;

use App\Repository\LineasComandasRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: LineasComandasRepository::class)]
#[ORM\Table(name: 'lineascomandas')]
class LineasComandas
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(name: 'idlinea')]
    private ?int $id = null;

    #[ORM\Column(type: Types::DECIMAL, precision: 8, scale: 2)]
    private ?string $cantidad = null;

    #[ORM\Column(options:['default'=>false])]
    private ?bool $entregado = null;

    #[ORM\ManyToOne(inversedBy: 'lineasComandas')]
    #[ORM\JoinColumn(name: 'idComanda', referencedColumnName: 'idComanda',nullable: false)]
    private ?Comandas $comanda = null;

    #[ORM\ManyToOne(inversedBy: 'lineasComandas')]
    #[ORM\JoinColumn(name: 'idProducto', referencedColumnName: 'idProducto',nullable: false)]
    private ?Productos $producto = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getCantidad(): ?string
    {
        return $this->cantidad;
    }

    public function setCantidad(string $cantidad): static
    {
        $this->cantidad = $cantidad;

        return $this;
    }

    public function isEntregado(): ?bool
    {
        return $this->entregado;
    }

    public function setEntregado(bool $entregado): static
    {
        $this->entregado = $entregado;

        return $this;
    }

    public function getComanda(): ?Comandas
    {
        return $this->comanda;
    }

    public function setComanda(?Comandas $comanda): static
    {
        $this->comanda = $comanda;

        return $this;
    }

    public function getProducto(): ?Productos
    {
        return $this->producto;
    }

    public function setProducto(?Productos $producto): static
    {
        $this->producto = $producto;

        return $this;
    }
}
