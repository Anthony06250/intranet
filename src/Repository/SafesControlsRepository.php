<?php

namespace App\Repository;

use App\Entity\SafesControls;
use App\Entity\Stores;
use DateInterval;
use DateTimeImmutable;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\ORM\NoResultException;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<SafesControls>
 *
 * @method SafesControls|null find($id, $lockMode = null, $lockVersion = null)
 * @method SafesControls|null findOneBy(array $criteria, array $orderBy = null)
 * @method SafesControls[]    findAll()
 * @method SafesControls[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class SafesControlsRepository extends ServiceEntityRepository
{
    /**
     * @param ManagerRegistry $registry
     */
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, SafesControls::class);
    }

    /**
     * @param SafesControls $entity
     * @param bool $flush
     * @return void
     */
    public function save(SafesControls $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    /**
     * @param SafesControls $entity
     * @param bool $flush
     * @return void
     */
    public function remove(SafesControls $entity, bool $flush = false): void
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
    public function countSafesControls(): mixed
    {
        return $this->createQueryBuilder('sc')
            ->select('COUNT(sc.id)')
            ->getQuery()
            ->getSingleScalarResult();
    }

    /**
     * @param DateTimeImmutable $month
     * @param Stores $store
     * @return mixed
     */
    public function findSafesControlsForSafe(DateTimeImmutable $month, Stores $store): mixed
    {
        $from = $month->format('Y-m-d') . ' 00:00:00';
        $to = $month->add(new DateInterval('P1M'))->format('Y-m-d') . ' 00:00:00';

        return $this->createQueryBuilder('sc')
            ->andWhere('sc.created_at BETWEEN :from AND :to')
            ->setParameter('from', $from)
            ->setParameter('to', $to)
            ->andWhere('sc.store = :store')
            ->setParameter('store', $store->getId())
            ->orderBy('sc.created_at', 'DESC')
            ->getQuery()
            ->getResult();
    }

//    /**
//     * @return SafesControls[] Returns an array of SafesControls objects
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

//    public function findOneBySomeField($value): ?SafesControls
//    {
//        return $this->createQueryBuilder('s')
//            ->andWhere('s.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
