<?php

namespace App\Repository;

use App\Entity\AdvancesPaymentsMethods;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<AdvancesPaymentsMethods>
 *
 * @method AdvancesPaymentsMethods|null find($id, $lockMode = null, $lockVersion = null)
 * @method AdvancesPaymentsMethods|null findOneBy(array $criteria, array $orderBy = null)
 * @method AdvancesPaymentsMethods[]    findAll()
 * @method AdvancesPaymentsMethods[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class AdvancesPaymentsMethodsRepository extends ServiceEntityRepository
{
    /**
     * @param ManagerRegistry $registry
     */
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, AdvancesPaymentsMethods::class);
    }

    /**
     * @param AdvancesPaymentsMethods $entity
     * @param bool $flush
     * @return void
     */
    public function save(AdvancesPaymentsMethods $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    /**
     * @param AdvancesPaymentsMethods $entity
     * @param bool $flush
     * @return void
     */
    public function remove(AdvancesPaymentsMethods $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

//    /**
//     * @return AdvancesPaymentsMethods[] Returns an array of AdvancesPaymentsMethods objects
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

//    public function findOneBySomeField($value): ?AdvancesPaymentsMethods
//    {
//        return $this->createQueryBuilder('a')
//            ->andWhere('a.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
