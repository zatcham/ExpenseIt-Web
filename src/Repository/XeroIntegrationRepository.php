<?php

namespace App\Repository;

use App\Entity\XeroIntegration;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<XeroIntegration>
 *
 * @method XeroIntegration|null find($id, $lockMode = null, $lockVersion = null)
 * @method XeroIntegration|null findOneBy(array $criteria, array $orderBy = null)
 * @method XeroIntegration[]    findAll()
 * @method XeroIntegration[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class XeroIntegrationRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, XeroIntegration::class);
    }

    //    /**
    //     * @return XeroIntegration[] Returns an array of XeroIntegration objects
    //     */
    //    public function findByExampleField($value): array
    //    {
    //        return $this->createQueryBuilder('x')
    //            ->andWhere('x.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->orderBy('x.id', 'ASC')
    //            ->setMaxResults(10)
    //            ->getQuery()
    //            ->getResult()
    //        ;
    //    }

    //    public function findOneBySomeField($value): ?XeroIntegration
    //    {
    //        return $this->createQueryBuilder('x')
    //            ->andWhere('x.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }
}
