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
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, UsersPermissions::class);
    }

    public function save(UsersPermissions $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(UsersPermissions $entity, bool $flush = false): void
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
    public function countUsersPermissions(): mixed
    {
        return $this->createQueryBuilder('p')
            ->select('count(p.id) as count')
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
