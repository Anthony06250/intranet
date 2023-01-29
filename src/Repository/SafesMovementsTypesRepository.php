<?php

namespace App\Repository;

use App\Entity\SafesMovementsTypes;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<SafesMovementsTypes>
 *
 * @method SafesMovementsTypes|null find($id, $lockMode = null, $lockVersion = null)
 * @method SafesMovementsTypes|null findOneBy(array $criteria, array $orderBy = null)
 * @method SafesMovementsTypes[]    findAll()
 * @method SafesMovementsTypes[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class SafesMovementsTypesRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, SafesMovementsTypes::class);
    }

    public function save(SafesMovementsTypes $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(SafesMovementsTypes $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

//    /**
//     * @return SafesMovementsTypes[] Returns an array of SafesMovementsTypes objects
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

//    public function findOneBySomeField($value): ?SafesMovementsTypes
//    {
//        return $this->createQueryBuilder('s')
//            ->andWhere('s.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
