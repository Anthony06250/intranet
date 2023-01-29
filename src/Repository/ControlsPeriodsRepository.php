<?php

namespace App\Repository;

use App\Entity\ControlsPeriods;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<ControlsPeriods>
 *
 * @method ControlsPeriods|null find($id, $lockMode = null, $lockVersion = null)
 * @method ControlsPeriods|null findOneBy(array $criteria, array $orderBy = null)
 * @method ControlsPeriods[]    findAll()
 * @method ControlsPeriods[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ControlsPeriodsRepository extends ServiceEntityRepository
{
    /**
     * @param ManagerRegistry $registry
     */
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, ControlsPeriods::class);
    }

    /**
     * @param ControlsPeriods $entity
     * @param bool $flush
     * @return void
     */
    public function save(ControlsPeriods $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    /**
     * @param ControlsPeriods $entity
     * @param bool $flush
     * @return void
     */
    public function remove(ControlsPeriods $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

//    /**
//     * @return Periods[] Returns an array of Periods objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('p')
//            ->andWhere('p.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('p.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?Periods
//    {
//        return $this->createQueryBuilder('p')
//            ->andWhere('p.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
