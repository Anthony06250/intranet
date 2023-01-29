<?php

namespace App\Repository;

use App\Entity\CustomersTypesIds;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<CustomersTypesIds>
 *
 * @method CustomersTypesIds|null find($id, $lockMode = null, $lockVersion = null)
 * @method CustomersTypesIds|null findOneBy(array $criteria, array $orderBy = null)
 * @method CustomersTypesIds[]    findAll()
 * @method CustomersTypesIds[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class CustomersTypesIdsRepository extends ServiceEntityRepository
{
    /**
     * @param ManagerRegistry $registry
     */
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, CustomersTypesIds::class);
    }

    /**
     * @param CustomersTypesIds $entity
     * @param bool $flush
     * @return void
     */
    public function save(CustomersTypesIds $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    /**
     * @param CustomersTypesIds $entity
     * @param bool $flush
     * @return void
     */
    public function remove(CustomersTypesIds $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

//    /**
//     * @return CustomersTypesId[] Returns an array of CustomersTypesId objects
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

//    public function findOneBySomeField($value): ?CustomersTypesId
//    {
//        return $this->createQueryBuilder('c')
//            ->andWhere('c.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
