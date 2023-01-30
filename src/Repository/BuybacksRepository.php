<?php

namespace App\Repository;

use App\DBAL\Types\BuybacksStatusesType;
use App\Entity\Buybacks;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\ORM\NoResultException;
use Doctrine\ORM\QueryBuilder;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Buybacks>
 *
 * @method Buybacks|null find($id, $lockMode = null, $lockVersion = null)
 * @method Buybacks|null findOneBy(array $criteria, array $orderBy = null)
 * @method Buybacks[]    findAll()
 * @method Buybacks[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class BuybacksRepository extends ServiceEntityRepository
{
    /**
     * @param ManagerRegistry $registry
     */
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Buybacks::class);
    }

    /**
     * @param Buybacks $entity
     * @param bool $flush
     * @return void
     */
    public function save(Buybacks $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    /**
     * @param Buybacks $entity
     * @param bool $flush
     * @return void
     */
    public function remove(Buybacks $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    /**
     * @return mixed
     * @throws NoResultException
     * @throws NonUniqueResultException
     */
    public function countBuybacks(): mixed
    {
        return $this->createQueryBuilder('b')
            ->select('count(b.id) as count')
            ->getQuery()
            ->getSingleScalarResult();
    }

    /**
     * @return int
     * @throws NoResultException
     * @throws NonUniqueResultException
     */
    public function countPendingBuybacksToExpired(): int
    {
        return $this->findPendingBuybacksToExpired()
            ->select('COUNT(b.id)')
            ->getQuery()
            ->getSingleScalarResult();
    }

    /**
     * @return int
     */
    public function updatePendingBuybacksToExpired(): int
    {
        return $this->findPendingBuybacksToExpired()
            ->update()
            ->set('b.status', ':expired')
            ->setParameter(':expired', BuybacksStatusesType::STATE_EXPIRED)
            ->getQuery()
            ->execute();
    }

    /**
     * @return QueryBuilder
     */
    public function findPendingBuybacksToExpired(): QueryBuilder
    {
        return $this->createQueryBuilder('b')
            ->andWhere('b.status = :pending')
            ->andWhere('b.due_at < CURRENT_TIMESTAMP()')
            ->setParameter('pending', BuybacksStatusesType::STATE_PENDING);
    }

//    /**
//     * @return Buyback[] Returns an array of Buyback objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('b')
//            ->andWhere('b.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('b.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?Buyback
//    {
//        return $this->createQueryBuilder('b')
//            ->andWhere('b.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
