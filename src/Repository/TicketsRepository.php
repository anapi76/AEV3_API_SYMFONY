<?php

namespace App\Repository;

use App\Entity\Comandas;
use App\Entity\Tickets;
use DateTime;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Collections\Collection;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Tickets>
 *
 * @method Tickets|null find($id, $lockMode = null, $lockVersion = null)
 * @method Tickets|null findOneBy(array $criteria, array $orderBy = null)
 * @method Tickets[]    findAll()
 * @method Tickets[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class TicketsRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Tickets::class);
    }

    public function new(Comandas $comanda, float $importe, $flush): Tickets
    {
        try {
            $ticket = new Tickets();
            $ticket->setComanda($comanda);
            $ticket->setFecha(new DateTime());
            $comanda->addTicket($ticket);
            $ticket->setImporte($importe);
            $this->save($ticket, $flush);
            return $ticket;
        } catch (\Exception $e) {
            throw $e;
        }
    }

    public function ticketJSON(Tickets $ticket): ?array
    {
        if (is_null($ticket)) {
            $ticket = null;
        } else {
            $ticketJSON = array(
                $ticket->getId() => array(
                    'fecha' => $ticket->getFecha()->format('d/m/Y H:i:s'),
                    'idComanda' => $ticket->getComanda()->getId(),
                    'importe' => $ticket->getImporte()
                ),
                'lineasComanda' => array($this->lineasComandaJSON($ticket->getComanda()->getLineasComandas()))
            );
            return $ticketJSON;
        }
    }

    //Método que saca las líneas de comanda en formato json
    public function lineasComandaJSON(?Collection $lineasComanda): ?array
    {
        if (is_null($lineasComanda)) {
            $json = null;
        } else {
            $json = array();
            foreach ($lineasComanda as $linea) {
                $json[$linea->getId()] = array(
                    'PRODUCTO' => $linea->getProducto()->getNombre(),
                    'CANTIDAD' => $linea->getCantidad()
                );
            }
            return $json;
        }
    }

    //Función para hacer flush 
    public function save(Tickets $ticket, bool $flush = false): void
    {
        try {
            $this->getEntityManager()->persist($ticket);
            if ($flush) {
                $this->getEntityManager()->flush();
            }
        } catch (\Exception $e) {
            throw $e;
        }
    }

    public function testInsert(Tickets $ticket): bool
    {
        if (empty($ticket) || is_null($ticket)) {
            return false;
        } else {
            $entidad = $this->find($ticket);
            if (empty($entidad))
                return false;
            else {
                return true;
            }
        }
    }

    //    /**
    //     * @return Tickets[] Returns an array of Tickets objects
    //     */
    //    public function findByExampleField($value): array
    //    {
    //        return $this->createQueryBuilder('t')
    //            ->andWhere('t.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->orderBy('t.id', 'ASC')
    //            ->setMaxResults(10)
    //            ->getQuery()
    //            ->getResult()
    //        ;
    //    }

    //    public function findOneBySomeField($value): ?Tickets
    //    {
    //        return $this->createQueryBuilder('t')
    //            ->andWhere('t.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }
}
