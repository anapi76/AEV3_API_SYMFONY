<?php

namespace App\Entity;

use App\Repository\PedidosRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: PedidosRepository::class)]
#[ORM\Table(name: 'pedidos')]
class Pedidos
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(name: 'idPedidos')]
    private ?int $id = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    private ?\DateTimeInterface $fecha = null;

    #[ORM\Column(length: 100, nullable: true)]
    private ?string $detalles = null;

    #[ORM\Column(name: 'estado', options: ['default' => true])]
    private ?bool $estado = true;

    #[ORM\ManyToOne(inversedBy: 'pedidos')]
    #[ORM\JoinColumn(name: 'idProveedor', referencedColumnName: 'idProveedor')]
    private ?Proveedores $proveedor = null;

    #[ORM\OneToMany(mappedBy: 'pedido', targetEntity: LineasPedidos::class)]
    private Collection $lineasPedidos;

    public function __construct()
    {
        $this->lineasPedidos = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getFecha(): ?\DateTimeInterface
    {
        return $this->fecha;
    }

    public function setFecha(\DateTimeInterface $fecha): static
    {
        $this->fecha = $fecha;

        return $this;
    }

    public function getDetalles(): ?string
    {
        return $this->detalles;
    }

    public function setDetalles(?string $detalles): static
    {
        $this->detalles = $detalles;

        return $this;
    }

    public function isEstado(): ?bool
    {
        return $this->estado;
    }

    public function setEstado(bool $estado): static
    {
        $this->estado = $estado;

        return $this;
    }

    public function getProveedor(): ?Proveedores
    {
        return $this->proveedor;
    }

    public function setProveedor(?Proveedores $proveedor): static
    {
        $this->proveedor = $proveedor;

        return $this;
    }

    /**
     * @return Collection<int, LineasPedidos>
     */
    public function getLineasPedidos(): Collection
    {
        return $this->lineasPedidos;
    }

    public function addLineasPedido(LineasPedidos $lineasPedido): static
    {
        if (!$this->lineasPedidos->contains($lineasPedido)) {
            $this->lineasPedidos->add($lineasPedido);
            $lineasPedido->setPedido($this);
        }

        return $this;
    }

    public function removeLineasPedido(LineasPedidos $lineasPedido): static
    {
        if ($this->lineasPedidos->removeElement($lineasPedido)) {
            // set the owning side to null (unless already changed)
            if ($lineasPedido->getPedido() === $this) {
                $lineasPedido->setPedido(null);
            }
        }

        return $this;
    }
}
