<?php

namespace App\Repository;

use App\Entity\DepositsSales;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\ORM\NoResultException;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<DepositsSales>
 *
 * @method DepositsSales|null find($id, $lockMode = null, $lockVersion = null)
 * @method DepositsSales|null findOneBy(array $criteria, array $orderBy = null)
 * @method DepositsSales[]    findAll()
 * @method DepositsSales[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class DepositsSalesRepository extends ServiceEntityRepository
{
    /**
     * @param ManagerRegistry $registry
     */
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, DepositsSales::class);
    }

    /**
     * @param DepositsSales $entity
     * @param bool $flush
     * @return void
     */
    public function save(DepositsSales $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    /**
     * @param DepositsSales $entity
     * @param bool $flush
     * @return void
     */
    public function remove(DepositsSales $entity, bool $flush = false): void
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
    public function countDepositsSales(): int
    {
        return $this->createQueryBuilder('ds')
            ->select('COUNT(ds.id)')
            ->getQuery()
            ->getSingleScalarResult();
    }

//    /**
//     * @return DepositsSales[] Returns an array of DepositsSales objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('d')
//            ->andWhere('d.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('d.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?DepositsSales
//    {
//        return $this->createQueryBuilder('d')
//            ->andWhere('d.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
