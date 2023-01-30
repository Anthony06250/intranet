<?php

namespace App\Repository;

use App\Entity\SafesMovements;
use App\Entity\Stores;
use DateInterval;
use DateTimeImmutable;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\ORM\NoResultException;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<SafesMovements>
 *
 * @method SafesMovements|null find($id, $lockMode = null, $lockVersion = null)
 * @method SafesMovements|null findOneBy(array $criteria, array $orderBy = null)
 * @method SafesMovements[]    findAll()
 * @method SafesMovements[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class SafesMovementsRepository extends ServiceEntityRepository
{
    /**
     * @param ManagerRegistry $registry
     */
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, SafesMovements::class);
    }

    /**
     * @param SafesMovements $entity
     * @param bool $flush
     * @return void
     */
    public function save(SafesMovements $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    /**
     * @param SafesMovements $entity
     * @param bool $flush
     * @return void
     */
    public function remove(SafesMovements $entity, bool $flush = false): void
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
    public function countSafesMovements(): int
    {
        return $this->createQueryBuilder('sm')
            ->select('COUNT(sm.id)')
            ->getQuery()
            ->getSingleScalarResult();
    }

    /**
     * @param DateTimeImmutable $month
     * @param Stores $store
     * @return mixed
     */
    public function findSafesMovementsForSafe(DateTimeImmutable $month, Stores $store): mixed
    {
        $from = $month->format('Y-m-d') . ' 00:00:00';
        $to = $month->add(new DateInterval('P1M'))->format('Y-m-d') . ' 00:00:00';

        return $this->createQueryBuilder('sm')
            ->andWhere('sm.created_at BETWEEN :from AND :to')
            ->setParameter('from', $from)
            ->setParameter('to', $to)
            ->andWhere('sm.store = :store')
            ->setParameter('store', $store->getId())
            ->orderBy('sm.created_at', 'ASC')
            ->getQuery()
            ->getResult();
    }

//    /**
//     * @return SafesMovements[] Returns an array of SafesMovements objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('s')
//            ->andWhere('s.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('s.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?SafesMovements
//    {
//        return $this->createQueryBuilder('s')
//            ->andWhere('s.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
