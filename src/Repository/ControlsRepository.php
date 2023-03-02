<?php

namespace App\Repository;

use App\Entity\Controls;
use App\Entity\ControlsPeriods;
use App\Entity\Stores;
use DateInterval;
use DateTimeImmutable;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\ORM\NoResultException;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Controls>
 *
 * @method Controls|null find($id, $lockMode = null, $lockVersion = null)
 * @method Controls|null findOneBy(array $criteria, array $orderBy = null)
 * @method Controls[]    findAll()
 * @method Controls[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ControlsRepository extends ServiceEntityRepository
{
    /**
     * @param ManagerRegistry $registry
     */
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Controls::class);
    }

    /**
     * @param Controls $entity
     * @param bool $flush
     * @return void
     */
    public function save(Controls $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    /**
     * @param Controls $entity
     * @param bool $flush
     * @return void
     */
    public function remove(Controls $entity, bool $flush = false): void
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
    public function countControls(): int
    {
        return $this->createQueryBuilder('c')
            ->select('COUNT(c.id)')
            ->getQuery()
            ->getSingleScalarResult();
    }

    /**
     * @param DateTimeImmutable $month
     * @param Stores $store
     * @param ControlsPeriods $period
     * @return float|int|mixed|string
     */
    public function findControlsForSafe(DateTimeImmutable $month, Stores $store, ControlsPeriods $period): mixed
    {
        $from = $month->format('Y-m-d') . ' 00:00:00';
        $to = $month->add(new DateInterval('P1M'))->format('Y-m-d') . ' 00:00:00';

        return $this->createQueryBuilder('c')
            ->andWhere('c.period = :period')
            ->setParameter('period', $period->getId())
            ->andWhere('c.createdAt BETWEEN :from AND :to')
            ->setParameter('from', $from)
            ->setParameter('to', $to)
            ->andWhere('c.store = :store')
            ->setParameter('store', $store->getId())
            ->orderBy('c.createdAt', 'DESC')
            ->getQuery()
            ->getResult();
    }

    /**
     * @param Stores $store
     * @param int $counter
     * @param int $period
     * @return int
     */
    public function findTodayControl(Stores $store, int $counter, int $period): int
    {
        $from = (new DateTimeImmutable())->format('Y-m-d') . ' 00:00:00';
        $to = (new DateTimeImmutable())->format('Y-m-d') . ' 23:59:59';

        $result = $this->createQueryBuilder('c')
            ->select('c.id')
            ->andWhere('c.store = :store')
            ->setParameter('store', $store->getId())
            ->andWhere('c.counter = :counter')
            ->setParameter('counter', $counter)
            ->andWhere('c.period = :period')
            ->setParameter('period', $period)
            ->andWhere('c.createdAt BETWEEN :from AND :to')
            ->setParameter('from', $from)
            ->setParameter('to', $to)
            ->orderBy('c.createdAt', 'DESC')
            ->getQuery()
            ->getResult();

        return !empty($result) ? $result[0]['id'] : 0;
    }

//    /**
//     * @return Controls[] Returns an array of Controls objects
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

//    public function findOneBySomeField($value): ?Controls
//    {
//        return $this->createQueryBuilder('c')
//            ->andWhere('c.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
