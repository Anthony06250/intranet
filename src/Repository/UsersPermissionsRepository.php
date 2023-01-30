<?php

namespace App\Repository;

use App\Entity\UsersPermissions;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\ORM\NoResultException;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<UsersPermissions>
 *
 * @method UsersPermissions|null find($id, $lockMode = null, $lockVersion = null)
 * @method UsersPermissions|null findOneBy(array $criteria, array $orderBy = null)
 * @method UsersPermissions[]    findAll()
 * @method UsersPermissions[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class UsersPermissionsRepository extends ServiceEntityRepository
{
    /**
     * @param ManagerRegistry $registry
     */
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, UsersPermissions::class);
    }

    /**
     * @param UsersPermissions $entity
     * @param bool $flush
     * @return void
     */
    public function save(UsersPermissions $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    /**
     * @param UsersPermissions $entity
     * @param bool $flush
     * @return void
     */
    public function remove(UsersPermissions $entity, bool $flush = false): void
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
    public function countUsersPermissions(): int
    {
        return $this->createQueryBuilder('p')
            ->select('COUNT(p.id)')
            ->getQuery()
            ->getSingleScalarResult();
    }

//    /**
//     * @return UsersPermissions[] Returns an array of UsersPermissions objects
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

//    public function findOneBySomeField($value): ?UsersPermissions
//    {
//        return $this->createQueryBuilder('p')
//            ->andWhere('p.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
