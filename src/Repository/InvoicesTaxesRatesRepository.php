<?php

namespace App\Repository;

use App\Entity\InvoicesTaxesRates;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<InvoicesTaxesRates>
 *
 * @method InvoicesTaxesRates|null find($id, $lockMode = null, $lockVersion = null)
 * @method InvoicesTaxesRates|null findOneBy(array $criteria, array $orderBy = null)
 * @method InvoicesTaxesRates[]    findAll()
 * @method InvoicesTaxesRates[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class InvoicesTaxesRatesRepository extends ServiceEntityRepository
{
    /**
     * @param ManagerRegistry $registry
     */
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, InvoicesTaxesRates::class);
    }

    /**
     * @param InvoicesTaxesRates $entity
     * @param bool $flush
     * @return void
     */
    public function save(InvoicesTaxesRates $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    /**
     * @param InvoicesTaxesRates $entity
     * @param bool $flush
     * @return void
     */
    public function remove(InvoicesTaxesRates $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

//    /**
//     * @return InvoicesTaxesRates[] Returns an array of InvoicesTaxesRates objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('i')
//            ->andWhere('i.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('i.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?InvoicesTaxesRates
//    {
//        return $this->createQueryBuilder('i')
//            ->andWhere('i.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
