<?php

namespace App\Repository;

use App\Entity\Stores;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\ORM\NoResultException;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Stores>
 *
 * @method Stores|null find($id, $lockMode = null, $lockVersion = null)
 * @method Stores|null findOneBy(array $criteria, array $orderBy = null)
 * @method Stores[]    findAll()
 * @method Stores[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class StoresRepository extends ServiceEntityRepository
{
    /**
     * @param ManagerRegistry $registry
     */
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Stores::class);
    }

    /**
     * @param Stores $entity
     * @param bool $flush
     * @return void
     */
    public function save(Stores $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    /**
     * @param Stores $entity
     * @param bool $flush
     * @return void
     */
    public function remove(Stores $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    /**
     * @return float|int|mixed|string
     * @throws NoResultException
     * @throws NonUniqueResultException
     */
    public function countStores(): mixed
    {
        return $this->createQueryBuilder('s')
            ->select('count(s.id) as count')
            ->getQuery()
            ->getSingleScalarResult();
    }

//    /**
//     * @return Stores[] Returns an array of Stores objects
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

//    public function findOneBySomeField($value): ?Stores
//    {
//        return $this->createQueryBuilder('s')
//            ->andWhere('s.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
