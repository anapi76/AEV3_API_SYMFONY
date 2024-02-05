<?php

namespace App\Repository;

use App\Entity\Proveedores;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
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

    //método para devolver todos los proveedores en formato json
    public function proveedoresAllJSON(): mixed
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

    //método para devolver un proveedor en formato json
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

    //método que recibe los parámetro para añadir un proveedor nuevo a la bd
    public function new(string $nombre, string $cif, string $direccion, ?int $telefono, ?string $email, ?string $contacto, bool $flush): void
    {
        try {
            $proveedor = new Proveedores();
            $proveedor->setNombre($nombre);
            $proveedor->setCif($cif);
            $proveedor->setDireccion($direccion);
            if (!is_null($telefono)) $proveedor->setTelefono($telefono);
            if (!is_null($email)) $proveedor->setEmail($email);
            if (!is_null($contacto)) $proveedor->setContacto($contacto);
            $this->save($proveedor, $flush);
        } catch (\Exception $e) {
            throw $e;
        }
    }

    //método que recibe lso parámetros para actualizar un proveedor de la bd
    public function update(Proveedores $proveedor, ?string $nuevoNombre, ?string $cif, ?string $direccion, ?int $telefono, ?string $email, ?string $contacto, bool $flush): void
    {
        try {
            if (!is_null($nuevoNombre)) $proveedor->setNombre($nuevoNombre);
            if (!is_null($cif)) $proveedor->setCif($cif);
            if (!is_null($direccion)) $proveedor->setDireccion($direccion);
            if (!is_null($telefono)) $proveedor->setTelefono($telefono);
            if (!is_null($email)) $proveedor->setEmail($email);
            if (!is_null($contacto)) $proveedor->setContacto($contacto);
            $this->save($proveedor, $flush);
        } catch (\Exception $e) {
            throw $e;
        }
    }

    //método para borrar un proveedor de la bd
    public function remove(Proveedores $proveedor, bool $flush = false): void
    {
        try {
            $this->getEntityManager()->remove($proveedor);
            if ($flush) {
                $this->getEntityManager()->flush();
            }
        } catch (\Exception $e) {
            throw $e;
        }
    }

    //método para persistir y flushear los datos en la bd
    public function save(Proveedores $proveedor, bool $flush = false): void
    {
        try {
            $this->getEntityManager()->persist($proveedor);
            if ($flush) {
                $this->getEntityManager()->flush();
            }
        } catch (\Exception $e) {
            throw $e;
        }
    }

    public function testInsert(string $nombre): bool
    {
        if (empty($nombre) || is_null($nombre)) {
            return false;
        } else {
            $entidad = $this->findOneBy(['nombre' => $nombre]);
            if (empty($entidad))
                return false;
            else {
                return true;
            }
        }
    }

    public function testUpdate(Proveedores $proveedor): bool
    {
        if (empty($proveedor) || is_null($proveedor)) {
            return false;
        } else {
            $entidad = $this->find($proveedor);
            if (empty($entidad))
                return false;
            else {
                return true;
            }
        }
    }

    public function testDelete(Proveedores $proveedor): bool
    {
        if (empty($proveedor) || is_null($proveedor)) {
            return false;
        } else {
            $entidad = $this->find($proveedor);
            if (empty($entidad))
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
