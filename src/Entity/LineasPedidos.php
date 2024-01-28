<?php

namespace App\Entity;

use App\Repository\LineasPedidosRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: LineasPedidosRepository::class)]
#[ORM\Table(name: 'lineaspedidos')]
class LineasPedidos
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(name: 'idlinea')]
    private ?int $id = null;

    #[ORM\Column(type: Types::DECIMAL, precision: 8, scale: 2)]
    private ?string $cantidad = null;

    #[ORM\Column(options:['default'=>false])]
    private ?bool $entregado = null;

    #[ORM\ManyToOne(inversedBy: 'lineasPedidos')]
    #[ORM\JoinColumn(name: 'idPedido', referencedColumnName: 'idPedidos',nullable: false)]
    private ?Pedidos $pedido = null;

    #[ORM\ManyToOne(inversedBy: 'lineasPedidos')]
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

    public function getPedido(): ?Pedidos
    {
        return $this->pedido;
    }

    public function setPedido(?Pedidos $pedido): static
    {
        $this->pedido = $pedido;

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
