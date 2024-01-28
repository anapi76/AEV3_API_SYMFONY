<?php

namespace App\Entity;

use App\Repository\ComandasRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ComandasRepository::class)]
#[ORM\Table(name: 'comandas')]
class Comandas
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(name: 'idComanda')]
    private ?int $id = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    private ?\DateTimeInterface $fecha = null;

    #[ORM\Column]
    private ?int $comensales = null;

    #[ORM\Column(length: 250, nullable: true)]
    private ?string $detalles = null;

    #[ORM\Column(options: ['default' => true])]
    private ?bool $estado = null;

    #[ORM\ManyToOne(inversedBy: 'comandas')]
    #[ORM\JoinColumn(name: 'idMesa', referencedColumnName: 'idMesa',nullable: false)]
    private ?Mesa $mesa = null;

    #[ORM\OneToMany(mappedBy: 'comanda', targetEntity: LineasComandas::class)]
    private Collection $lineasComandas;

    #[ORM\OneToMany(mappedBy: 'comanda', targetEntity: Tickets::class)]
    private Collection $tickets;

    public function __construct()
    {
        $this->lineasComandas = new ArrayCollection();
        $this->tickets = new ArrayCollection();
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

    public function getComensales(): ?int
    {
        return $this->comensales;
    }

    public function setComensales(int $comensales): static
    {
        $this->comensales = $comensales;

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

    public function getMesa(): ?Mesa
    {
        return $this->mesa;
    }

    public function setMesa(?Mesa $mesa): static
    {
        $this->mesa = $mesa;

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
            $lineasComanda->setComanda($this);
        }

        return $this;
    }

    public function removeLineasComanda(LineasComandas $lineasComanda): static
    {
        if ($this->lineasComandas->removeElement($lineasComanda)) {
            // set the owning side to null (unless already changed)
            if ($lineasComanda->getComanda() === $this) {
                $lineasComanda->setComanda(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Tickets>
     */
    public function getTickets(): Collection
    {
        return $this->tickets;
    }

    public function addTicket(Tickets $ticket): static
    {
        if (!$this->tickets->contains($ticket)) {
            $this->tickets->add($ticket);
            $ticket->setComanda($this);
        }

        return $this;
    }

    public function removeTicket(Tickets $ticket): static
    {
        if ($this->tickets->removeElement($ticket)) {
            // set the owning side to null (unless already changed)
            if ($ticket->getComanda() === $this) {
                $ticket->setComanda(null);
            }
        }

        return $this;
    }
}
