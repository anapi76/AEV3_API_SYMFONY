<?php

namespace App\Repository;

use App\Entity\Productos;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Productos>
 *
 * @method Productos|null find($id, $lockMode = null, $lockVersion = null)
 * @method Productos|null findOneBy(array $criteria, array $orderBy = null)
 * @method Productos[]    findAll()
 * @method Productos[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ProductosRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Productos::class);
    }

    public function productosAllJSON(): mixed
    {
        $productos = $this->findAll();
        if (empty($productos)) {
            return null;
        } else {
            $json = array();
            foreach ($productos as $producto) {
                $json[$producto->getId()] = array(
                    'NOMBRE' => $producto->getNombre(),
                    'DESCRIPCION' => $producto->getDescripcion(),
                    'PRECIO' => $producto->getPrecio()
                );
            }
            return $json;
        }
    }

    public function productoJSON(Productos $producto): mixed
    {
        $json = array();
        $json[$producto->getId()] = array(
            'NOMBRE' => $producto->getNombre(),
            'DESCRIPCION' => $producto->getDescripcion(),
            'PRECIO' => $producto->getPrecio()
        );

        return $json;
    }

    public function new(string $nombre, float $precio, ?string $descripcion, $flush): bool
    {
        try {
            $producto = new Productos();
            $producto->setNombre($nombre);
            $producto->setPrecio($precio);
            if (!is_null($descripcion)) $producto->setDescripcion($descripcion);
            if($this->save($producto, $flush)){
                return true;
            }
        } catch (\Exception $e) {
            return false;
        }
    }

    public function update(Productos $producto, ?string $nombre, ?float $precio, ?string $descripcion, $flush): void
    {
        try {
            if (!is_null($nombre)) $producto->setNombre($nombre);
            if (!is_null($precio)) $producto->setPrecio($precio);
            if (!is_null($descripcion)) $producto->setDescripcion($descripcion);
            $this->save($producto);
        } catch (\Exception $e) {
            throw $e;
        }
    }

    public function save(Productos $producto, bool $flush = false):bool
    {
        try {
            $this->getEntityManager()->persist($producto);
            if ($flush) {
                $this->getEntityManager()->flush();
            }
            return true;
        } catch (\Exception $e) {
            return false;
        }
    }



    //    /**
    //     * @return Productos[] Returns an array of Productos objects
    //     */
    //    public function findByExampleField($value): array
    //    {
    //        return $this->createQueryBuilder('p')
    //            ->andWhere('p.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->orderBy('p.id', 'ASC')
    //            ->setMaxResults(10)
    //            ->getQuery()
    //            ->getResult()
    //        ;
    //    }

    //    public function findOneBySomeField($value): ?Productos
    //    {
    //        return $this->createQueryBuilder('p')
    //            ->andWhere('p.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }
}
