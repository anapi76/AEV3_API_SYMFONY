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
                    'CONTACTO' => $proveedor->getContacto()
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
                'CONTACTO' => $proveedor->getContacto()
            );
        }
        return $json;
    }

    public function new(string $nombre, string $cif, string $direccion, ?int $telefono, ?string $email, ?string $contacto): void
    {
        $proveedor = new Proveedores();
        $proveedor->setNombre($nombre);
        $proveedor->setCif($cif);
        $proveedor->setDireccion($direccion);
        if(!is_null($telefono)) $proveedor->setTelefono($telefono);
        if(!is_null($email)) $proveedor->setEmail($email);
        if(!is_null($contacto)) $proveedor->setContacto($contacto);
        $this->save($proveedor,true);
    }

    public function update(Proveedores $proveedor, ?string $nuevoNombre, ?string $cif, ?string $direccion, ?int $telefono, ?string $email, ?string $contacto): void
    {
        if(!is_null($nuevoNombre))$proveedor->setNombre($nuevoNombre);
        if(!is_null($cif))$proveedor->setCif($cif);
        if(!is_null($direccion))$proveedor->setDireccion($direccion);
        if(!is_null($telefono)) $proveedor->setTelefono($telefono);
        if(!is_null($email)) $proveedor->setEmail($email);
        if(!is_null($contacto)) $proveedor->setContacto($contacto);
        $this->save($proveedor,true);
    }


    public function save(Proveedores $proveedor, bool $flush = false): void
    {
        $this->getEntityManager()->persist($proveedor);
        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(Proveedores $proveedor, bool $flush = false): void
    {
        $this->getEntityManager()->remove($proveedor);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function testInsert(?string $nombre): bool
    {
        if (is_null($nombre) || empty($nombre)) {
            return false;
        } else {
            $entidad = $this->findOneBy(['nombre'=>$nombre]);
            if (is_null($entidad))
                return false;
            else {
                return true;
            }
        }
    }
    public function testDelete(Proveedores $proveedor): bool
    {
        if (is_null($proveedor) ) {
            return false;
        } else {
            $entidad = $this->find($proveedor);
            if (is_null($entidad))
                return true;
            else {
                return false;
            }
        }
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
