<?php

namespace App\Repository;

use App\Entity\Safes;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\ORM\NoResultException;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Safes>
 *
 * @method Safes|null find($id, $lockMode = null, $lockVersion = null)
 * @method Safes|null findOneBy(array $criteria, array $orderBy = null)
 * @method Safes[]    findAll()
 * @method Safes[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class SafesRepository extends ServiceEntityRepository
{
    /**
     * @param ManagerRegistry $registry
     */
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Safes::class);
    }

    /**
     * @param Safes $entity
     * @param bool $flush
     * @return void
     */
    public function save(Safes $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    /**
     * @param Safes $entity
     * @param bool $flush
     * @return void
     */
    public function remove(Safes $entity, bool $flush = false): void
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
    public function countSafes(): mixed
    {
        return $this->createQueryBuilder('s')
            ->select('count(s.id) as count')
            ->getQuery()
            ->getSingleScalarResult();
    }

//    /**
//     * @return Safes[] Returns an array of Safes objects
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

//    public function findOneBySomeField($value): ?Safes
//    {
//        return $this->createQueryBuilder('s')
//            ->andWhere('s.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
