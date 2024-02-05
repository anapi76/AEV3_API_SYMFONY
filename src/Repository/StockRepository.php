<?php

namespace App\Repository;

use App\Entity\Productos;
use App\Entity\Stock;
use DateTime;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Stock>
 *
 * @method Stock|null find($id, $lockMode = null, $lockVersion = null)
 * @method Stock|null findOneBy(array $criteria, array $orderBy = null)
 * @method Stock[]    findAll()
 * @method Stock[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class StockRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Stock::class);
    }

    public function stockProductoJSON(Productos $producto): mixed
    {
        $stockProducto = $this->findBy(['producto' => $producto]);
        $json = array();
        foreach ($stockProducto as $stock) {
            $json[] = [
                'FECHA' => $stock->getFecha()->format('d-m-Y H:i:s'),
                'CANTIDAD' => $stock->getCantidad()
            ];
        }
        return $json;
    }

    public function stockFechaJSON(DateTime $fecha, array $productos): mixed
    {
        $json = array();
        foreach ($productos as $producto) {
            $arrayStock = $this->findBy(['producto' => $producto]);
            if (empty($arrayStock)) {
                $json[] = [
                    'PRODUCTO' => $producto->getNombre(),
                    'FECHA' => $fecha->format('d-m-Y H:i:s'),
                    'CANTIDAD' => 0
                ];
            }
            $ultimoStock = null;
            foreach ($arrayStock as $stock) {
                $stockFecha = $stock->getFecha();
                if ($stockFecha <= $fecha) {
                    if (is_null($ultimoStock) || $stockFecha > $ultimoStock->getFecha()) {
                        $ultimoStock = $stock;
                    }
                }
            }
            if (!is_null($ultimoStock)) {
                $json[] = [
                    'PRODUCTO' => $ultimoStock->getProducto()->getNombre(),
                    'FECHA' => $ultimoStock->getFecha()->format('d-m-Y H:i:s'),
                    'CANTIDAD' => $ultimoStock->getCantidad()
                ];
            }
        }
        return $json;
    }

    public function inventarioJSON(DateTime $fecha, array $productos): mixed
    {
        $json = array();
        foreach ($productos as $producto) {
            $arrayStock = $this->findBy(['producto' => $producto]);
            if (empty($arrayStock)) {
                $descripcion = (is_null($producto->getDescripcion())) ? '' : $producto->getDescripcion();
                $json[] = [
                    'PRODUCTO' => $producto->getNombre(),
                    'DESCRIPCION' => $descripcion,
                    'PRECIO' => $producto->getPrecio(),
                    'FECHA' => $fecha->format('d-m-Y H:i:s'),
                    'CANTIDAD' => 0
                ];
            }
            $ultimoStock = null;
            foreach ($arrayStock as $stock) {
                $stockFecha = $stock->getFecha();
                if ($stockFecha <= $fecha) {
                    if (is_null($ultimoStock) || $stockFecha > $ultimoStock->getFecha()) {
                        $ultimoStock = $stock;
                    }
                }
            }
            if (!is_null($ultimoStock)) {
                $descripcion = (is_null($ultimoStock->getProducto()->getDescripcion())) ? '' : $ultimoStock->getProducto()->getDescripcion();
                $json[] = [
                    'PRODUCTO' => $ultimoStock->getProducto()->getNombre(),
                    'DESCRIPCION' => $descripcion,
                    'PRECIO' => $ultimoStock->getProducto()->getPrecio(),
                    'FECHA' => $ultimoStock->getFecha()->format('d-m-Y H:i:s'),
                    'CANTIDAD' => $ultimoStock->getCantidad()
                ];
            }
        }
        return $json;
    }

    //Método que devuelve el stock con fecha más reciente del producto que le pasamos
    public function stockProducto(Productos $producto): ?Stock
    {
        $data = $this->findBy(['producto' => $producto], ['fecha' => 'DESC']);
        if (empty($data)) {
            $stock = null;
        } else {
            $stock = $data[0];
        }
        return $stock;
    }

    public function newStock(float $cantidad, Productos $producto): Stock
    {
        $newStock = new Stock();
        $newStock->setFecha(new DateTime());
        $newStock->setProducto($producto);
        $newStock->setCantidad($cantidad);

        $this->persist($newStock);
        $this->save(true);
        return $newStock;
    }

    //Función para persitir la entidad
    public function persist(Stock $stock): void
    {
        $this->getEntityManager()->persist($stock);
    }

    //Función para hacer flush 
    public function save(bool $flush = false): void
    {
        try {
            if ($flush) {
                $this->getEntityManager()->flush();
            }
        } catch (\Exception $e) {
            throw $e;
        }
    }

    public function testInsert(Stock $stock): bool
    {
        if (empty($stock) || is_null($stock)) {
            return false;
        } else {
            $entidad = $this->find($stock);
            if (empty($entidad))
                return false;
            else {
                return true;
            }
        }
    }

    //    /**
    //     * @return Stock[] Returns an array of Stock objects
    //     */
    //    public function findByExampleField($value): array
    //    {
    //        return $this->createQueryBuilder('s')
    //            ->andWhere('s.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->orderBy('s.id', 'ASC')
    //            ->setMaxResults(10)
    //            ->getQuery()
    //            ->getResult()
    //        ;
    //    }

    //    public function findOneBySomeField($value): ?Stock
    //    {
    //        return $this->createQueryBuilder('s')
    //            ->andWhere('s.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }
}
