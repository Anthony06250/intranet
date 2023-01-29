<?php

namespace App\Repository;

use App\Entity\ControlsCounters;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<ControlsCounters>
 *
 * @method ControlsCounters|null find($id, $lockMode = null, $lockVersion = null)
 * @method ControlsCounters|null findOneBy(array $criteria, array $orderBy = null)
 * @method ControlsCounters[]    findAll()
 * @method ControlsCounters[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ControlsCountersRepository extends ServiceEntityRepository
{
    /**
     * @param ManagerRegistry $registry
     */
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, ControlsCounters::class);
    }

    /**
     * @param ControlsCounters $entity
     * @param bool $flush
     * @return void
     */
    public function save(ControlsCounters $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    /**
     * @param ControlsCounters $entity
     * @param bool $flush
     * @return void
     */
    public function remove(ControlsCounters $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

//    /**
//     * @return Counters[] Returns an array of Counters objects
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

//    public function findOneBySomeField($value): ?Counters
//    {
//        return $this->createQueryBuilder('c')
//            ->andWhere('c.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
