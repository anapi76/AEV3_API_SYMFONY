<?php

namespace App\Repository;

use App\Entity\LineasPedidos;
use App\Entity\Pedidos;
use App\Entity\Productos;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Collections\Collection;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<LineasPedidos>
 *
 * @method LineasPedidos|null find($id, $lockMode = null, $lockVersion = null)
 * @method LineasPedidos|null findOneBy(array $criteria, array $orderBy = null)
 * @method LineasPedidos[]    findAll()
 * @method LineasPedidos[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class LineasPedidosRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, LineasPedidos::class);
    }

    public function persist(LineasPedidos $lineasPedido):void{
        $this->getEntityManager()->persist($lineasPedido);
    }

//    /**
//     * @return LineasPedidos[] Returns an array of LineasPedidos objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('l')
//            ->andWhere('l.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('l.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?LineasPedidos
//    {
//        return $this->createQueryBuilder('l')
//            ->andWhere('l.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
