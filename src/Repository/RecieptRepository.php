<?php

namespace App\Repository;

use App\Entity\Reciept;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Reciept>
 *
 * @method Reciept|null find($id, $lockMode = null, $lockVersion = null)
 * @method Reciept|null findOneBy(array $criteria, array $orderBy = null)
 * @method Reciept[]    findAll()
 * @method Reciept[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class RecieptRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Reciept::class);
    }

//    /**
//     * @return Reciept[] Returns an array of Reciept objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('r')
//            ->andWhere('r.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('r.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?Reciept
//    {
//        return $this->createQueryBuilder('r')
//            ->andWhere('r.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
