<?php

namespace App\Repository;

use App\Entity\LineasComandas;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<LineasComandas>
 *
 * @method LineasComandas|null find($id, $lockMode = null, $lockVersion = null)
 * @method LineasComandas|null findOneBy(array $criteria, array $orderBy = null)
 * @method LineasComandas[]    findAll()
 * @method LineasComandas[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class LineasComandasRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, LineasComandas::class);
    }

//    /**
//     * @return LineasComandas[] Returns an array of LineasComandas objects
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

//    public function findOneBySomeField($value): ?LineasComandas
//    {
//        return $this->createQueryBuilder('l')
//            ->andWhere('l.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
