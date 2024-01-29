<?php

namespace App\Repository;

use App\Entity\Proveedores;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Collections\Collection;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Proveedores>
 *
 * @method Proveedores|null find($id, $lockMode = null, $lockVersion = null)
 * @method Proveedores|null findOneBy(array $criteria, array $orderBy = null)
 * @method Proveedores[]    findAll()
 * @method Proveedores[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ProveedoresRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Proveedores::class);
    }

    public function proveedoresJSON(): mixed
    {
        $proveedores = $this->findAll();
        if (empty($proveedores)) {
            return null;
        } else {
            $json = array();
            foreach ($proveedores as $proveedor) {
                $json[$proveedor->getId()] = array(
                    'NOMBRE' => $proveedor->getNombre(),
                    'CIF' => $proveedor->getCif(),
                    'DIRECCION' => $proveedor->getDireccion(),
                    'TELEFONO' => $proveedor->getTelefono(),
                    'EMAIL' => $proveedor->getEmail(),
                    'CONTACTO' => $proveedor->getContacto(),
                    'PEDIDOS'=>$this->pedidosJSON($proveedor->getPedidos())
                );
            }
            return $json;
        }
    }
    public function proveedorJSON(int $id): mixed
    {
        $proveedor = $this->find($id);
        if (is_null($proveedor)) {
            return null;
        } else {
            $json = array();
            $json[$proveedor->getId()] = array(
                'NOMBRE' => $proveedor->getNombre(),
                'CIF' => $proveedor->getCif(),
                'DIRECCION' => $proveedor->getDireccion(),
                'TELEFONO' => $proveedor->getTelefono(),
                'EMAIL' => $proveedor->getEmail(),
                'CONTACTO' => $proveedor->getContacto(),
                'PEDIDOS'=>$this->pedidosJSON($proveedor->getPedidos())
            );
        }
        return $json;
    }

    public function pedidosJSON(Collection $pedidos): mixed
    {
        if (is_null($pedidos)) {
            return null;
        } else {
            $json = array();
            foreach($pedidos as $pedido){
                $estado=($pedido->isEstado())?'Creado':'Entregado';
                $json[$pedido->getId()] = array(
                    'PROVEEDOR' => $pedido->getProveedor()->getNombre(),
                    'FECHA' => $pedido->getFecha(),
                    'DETALLES' => $pedido->getDetalles(),
                    'ESTADO' => $estado
                );
            }
        }
        return $json;
    }






    //    /**
    //     * @return Proveedores[] Returns an array of Proveedores objects
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

    //    public function findOneBySomeField($value): ?Proveedores
    //    {
    //        return $this->createQueryBuilder('p')
    //            ->andWhere('p.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }
}
