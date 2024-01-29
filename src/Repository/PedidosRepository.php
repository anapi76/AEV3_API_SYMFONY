<?php

namespace App\Repository;

use App\Entity\Pedidos;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Pedidos>
 *
 * @method Pedidos|null find($id, $lockMode = null, $lockVersion = null)
 * @method Pedidos|null findOneBy(array $criteria, array $orderBy = null)
 * @method Pedidos[]    findAll()
 * @method Pedidos[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class PedidosRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Pedidos::class);
    }
    //Función que comprueba si el pedido se ha insertado correctamente en la BD
    public function testInsert(?Pedidos $pedido): bool
    {
        if (empty($pedido) || is_null($pedido)) {
            return false;
        } else {
            $entidad = $this->find($pedido);
            if (empty($entidad))
                return false;
            else {
                return true;
            }
        }
    }

    public function persist(Pedidos $pedido):void{
        $this->getEntityManager()->persist($pedido);
    }

    public function save(bool $flush = false): void
    {
        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }


    //    /**
    //     * @return Pedidos[] Returns an array of Pedidos objects
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

    //    public function findOneBySomeField($value): ?Pedidos
    //    {
    //        return $this->createQueryBuilder('p')
    //            ->andWhere('p.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }
}
