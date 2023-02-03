<?php

namespace App\Repository;

use App\Entity\AdvancesPayments;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\ORM\NoResultException;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<AdvancesPayments>
 *
 * @method AdvancesPayments|null find($id, $lockMode = null, $lockVersion = null)
 * @method AdvancesPayments|null findOneBy(array $criteria, array $orderBy = null)
 * @method AdvancesPayments[]    findAll()
 * @method AdvancesPayments[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class AdvancesPaymentsRepository extends ServiceEntityRepository
{
    /**
     * @param ManagerRegistry $registry
     */
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, AdvancesPayments::class);
    }

    /**
     * @param AdvancesPayments $entity
     * @param bool $flush
     * @return void
     */
    public function save(AdvancesPayments $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    /**
     * @param AdvancesPayments $entity
     * @param bool $flush
     * @return void
     */
    public function remove(AdvancesPayments $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    /**
     * @return int
     * @throws NoResultException
     * @throws NonUniqueResultException
     */
    public function countAdvancesPayments(): int
    {
        return $this->createQueryBuilder('ap')
            ->select('COUNT(ap.id)')
            ->getQuery()
            ->getSingleScalarResult();
    }

//    /**
//     * @return AdvancesPayments[] Returns an array of AdvancesPayments objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('a')
//            ->andWhere('a.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('a.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?AdvancesPayments
//    {
//        return $this->createQueryBuilder('a')
//            ->andWhere('a.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
