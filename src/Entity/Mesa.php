<?php

namespace App\Entity;

use App\Repository\MesaRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: MesaRepository::class)]
#[ORM\Table(name: 'mesa')]
class Mesa
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(name: 'idMesa')]
    private ?int $id = null;

    #[ORM\Column(length: 50, unique: true)]
    private ?string $nombre = null;

    #[ORM\Column]
    private ?int $comensales = null;

    #[ORM\OneToMany(mappedBy: 'mesa', targetEntity: Comandas::class)]
    private Collection $comandas;

    public function __construct()
    {
        $this->comandas = new ArrayCollection();
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

    public function getComensales(): ?int
    {
        return $this->comensales;
    }

    public function setComensales(int $comensales): static
    {
        $this->comensales = $comensales;

        return $this;
    }

    /**
     * @return Collection<int, Comandas>
     */
    public function getComandas(): Collection
    {
        return $this->comandas;
    }

    public function addComanda(Comandas $comanda): static
    {
        if (!$this->comandas->contains($comanda)) {
            $this->comandas->add($comanda);
            $comanda->setMesa($this);
        }

        return $this;
    }

    public function removeComanda(Comandas $comanda): static
    {
        if ($this->comandas->removeElement($comanda)) {
            // set the owning side to null (unless already changed)
            if ($comanda->getMesa() === $this) {
                $comanda->setMesa(null);
            }
        }

        return $this;
    }
}
