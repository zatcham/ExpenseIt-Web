<?php

namespace App\Repository;

use App\Entity\Request;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Request>
 *
 * @method Request|null find($id, $lockMode = null, $lockVersion = null)
 * @method Request|null findOneBy(array $criteria, array $orderBy = null)
 * @method Request[]    findAll()
 * @method Request[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class RequestRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Request::class);
    }

    public function getMonthlyForDepartment($deptId) {
        $qb = $this->createQueryBuilder('r');

        $qb->select('YEAR(r.timestamp) AS year', 'MONTH(r.timestamp) AS month', 'SUM(r.price) AS total_price')
            ->where('r.department = :deptId')
            ->andWhere('r.timestamp > :oneYearAgo')
            ->groupBy('year', 'month')
            ->orderBy('year', 'ASC')
            ->addOrderBy('month', 'DESC')
            ->setParameter('deptId', $deptId)
            ->setParameter('oneYearAgo', new \DateTime('-1 year'));

        return $qb->getQuery()->getResult();
    }

    public function getWeeklyForUser($userId) {
        $qb = $this->createQueryBuilder('r');

        $qb->select('YEAR(r.timestamp) AS year', 'WEEK(r.timestamp) AS week', 'SUM(r.price) AS total_price', 'COUNT(r.id) AS count')
            ->where('r.User = :userId')
            ->andWhere('r.timestamp > :oneYearAgo')
            ->groupBy('year', 'week')
            ->orderBy('year', 'ASC')
            ->addOrderBy('week', 'DESC')
            ->setParameter('userId', $userId)
            ->setParameter('oneYearAgo', new \DateTime('-1 year'));

        return $qb->getQuery()->getResult();
    }

//    /**
//     * @return Request[] Returns an array of Request objects
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

//    public function findOneBySomeField($value): ?Request
//    {
//        return $this->createQueryBuilder('r')
//            ->andWhere('r.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
