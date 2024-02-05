<?php

namespace App\Repository;

use App\Entity\Comandas;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Comandas>
 *
 * @method Comandas|null find($id, $lockMode = null, $lockVersion = null)
 * @method Comandas|null findOneBy(array $criteria, array $orderBy = null)
 * @method Comandas[]    findAll()
 * @method Comandas[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ComandasRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Comandas::class);
    }

    //Función para persitir la entidad
    public function persist(Comandas $comanda): void
    {
        $this->getEntityManager()->persist($comanda);
    }

    //Función para hacer flush 
    public function save(bool $flush=false): void
    {
        try {
            if($flush){
                $this->getEntityManager()->flush();
            }
        } catch (\Exception $e) {
            throw $e;
        }
    }

    public function testInsert(Comandas $comanda):bool{
        if (empty($comanda) || is_null($comanda)) {
            return false;
        } else {
            $entidad = $this->find($comanda);
            if (empty($entidad))
                return false;
            else {
                return true;
            }
        }
    }


//    /**
//     * @return Comandas[] Returns an array of Comandas objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('c')
//            ->andWhere('c.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('c.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?Comandas
//    {
//        return $this->createQueryBuilder('c')
//            ->andWhere('c.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
