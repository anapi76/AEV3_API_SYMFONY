<?php

namespace App\Entity;

use App\Repository\ProductosRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ProductosRepository::class)]
#[ORM\Table(name: 'productos')]
class Productos
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(name: 'idProducto')]
    private ?int $id = null;

    #[ORM\Column(length: 50,unique: true)]
    private ?string $nombre = null;

    #[ORM\Column(length: 100, nullable: true)]
    private ?string $descripcion = null;

    #[ORM\Column(type: Types::DECIMAL, precision: 8, scale: 2,options: ['default' => 0.0])]
    private ?string $precio = null;

    #[ORM\OneToMany(mappedBy: 'producto', targetEntity: LineasPedidos::class)]
    private Collection $lineasPedidos;

    #[ORM\OneToMany(mappedBy: 'producto', targetEntity: Stock::class)]
    private Collection $stocks;

    #[ORM\OneToMany(mappedBy: 'producto', targetEntity: LineasComandas::class)]
    private Collection $lineasComandas;

    public function __construct()
    {
        $this->lineasPedidos = new ArrayCollection();
        $this->stocks = new ArrayCollection();
        $this->lineasComandas = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getNombre(): ?string
    {
        return $this->nombre;
    }

    public function setNombre(string $nombre): static
    {
        $this->nombre = $nombre;

        return $this;
    }

    public function getDescripcion(): ?string
    {
        return $this->descripcion;
    }

    public function setDescripcion(?string $descripcion): static
    {
        $this->descripcion = $descripcion;

        return $this;
    }

    public function getPrecio(): ?string
    {
        return $this->precio;
    }

    public function setPrecio(string $precio): static
    {
        $this->precio = $precio;

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
            $lineasPedido->setProducto($this);
        }

        return $this;
    }

    public function removeLineasPedido(LineasPedidos $lineasPedido): static
    {
        if ($this->lineasPedidos->removeElement($lineasPedido)) {
            // set the owning side to null (unless already changed)
            if ($lineasPedido->getProducto() === $this) {
                $lineasPedido->setProducto(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Stock>
     */
    public function getStocks(): Collection
    {
        return $this->stocks;
    }

    public function addStock(Stock $stock): static
    {
        if (!$this->stocks->contains($stock)) {
            $this->stocks->add($stock);
            $stock->setProducto($this);
        }

        return $this;
    }

    public function removeStock(Stock $stock): static
    {
        if ($this->stocks->removeElement($stock)) {
            // set the owning side to null (unless already changed)
            if ($stock->getProducto() === $this) {
                $stock->setProducto(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, LineasComandas>
     */
    public function getLineasComandas(): Collection
    {
        return $this->lineasComandas;
    }

    public function addLineasComanda(LineasComandas $lineasComanda): static
    {
        if (!$this->lineasComandas->contains($lineasComanda)) {
            $this->lineasComandas->add($lineasComanda);
            $lineasComanda->setProducto($this);
        }

        return $this;
    }

    public function removeLineasComanda(LineasComandas $lineasComanda): static
    {
        if ($this->lineasComandas->removeElement($lineasComanda)) {
            // set the owning side to null (unless already changed)
            if ($lineasComanda->getProducto() === $this) {
                $lineasComanda->setProducto(null);
            }
        }

        return $this;
    }
}
