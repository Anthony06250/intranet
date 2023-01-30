<?php

namespace App\Repository;

use App\Entity\Safes;
use App\Entity\Stores;
use DateTimeImmutable;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\ORM\NoResultException;
use Doctrine\ORM\QueryBuilder;
use Doctrine\Persistence\ManagerRegistry;
use Exception;

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
     * @param StoresRepository $storesRepository
     * @param ControlsPeriodsRepository $controlsPeriodsRepository
     * @param ControlsCountersRepository $controlsCountersRepository
     * @param SafesMovementsTypesRepository $safesMovementsTypesRepository
     */
    public function __construct(ManagerRegistry $registry,
                                private readonly StoresRepository $storesRepository,
                                private readonly ControlsPeriodsRepository $controlsPeriodsRepository,
                                private readonly ControlsCountersRepository $controlsCountersRepository,
                                private readonly SafesMovementsTypesRepository $safesMovementsTypesRepository)
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
     * @return int
     * @throws NoResultException
     * @throws NonUniqueResultException
     */
    public function countSafes(): int
    {
        return $this->createQueryBuilder('s')
            ->select('COUNT(s.id)')
            ->getQuery()
            ->getSingleScalarResult();
    }

    /**
     * @return int
     * @throws NoResultException
     * @throws NonUniqueResultException
     */
    public function countMissingSafesForCurrentMonth(): int
    {
        $countSafes = $this->findMissingSafesForCurrentMonth()
            ->select('COUNT(b.id)')
            ->getQuery()
            ->getSingleScalarResult();

        return $this->storesRepository->countStores() - $countSafes;
    }

    /**
     * @return int
     * @throws Exception
     */
    public function createMissingSafesForCurrentMonth(): int
    {
        $safes = $this->findMissingSafesForCurrentMonth()
            ->getQuery()
            ->getResult();
        $existStores = array_column(array_column($safes, 'store'), 'id');
        $stores = $this->storesRepository->findAll();

        foreach ($stores as $store) {
            if (!in_array($store->getId(), $existStores)) {
                $this->createMissingSafeForCurrentMonth($store);
            }
        }

        $this->getEntityManager()->flush();

        return count($stores) - count($safes);
    }

    /**
     * @param Stores $store
     * @return void
     * @throws Exception
     */
    private function createMissingSafeForCurrentMonth(Stores $store): void
    {
        $safe = new Safes();

        $safe->setMonth((new DateTimeImmutable())->format('Y-m') . '-01');
        $safe->setStore($store);
        $safe->setControlsPeriod($this->controlsPeriodsRepository->find(2));

        for ($i = 1; $i <= 2; $i++) {
            $controlsCounter = $this->controlsCountersRepository->find($i);

            $safe->addControlsCounters($controlsCounter);
        }

        for ($i = 1; $i <= 3; $i++) {
            $safesMovementsTypes = $this->safesMovementsTypesRepository->find($i);

            $safe->addSafesMovementsTypes($safesMovementsTypes);
        }

        $safe->setCreatedAt(new DateTimeImmutable());
        $safe->setUpdatedAt(new DateTimeImmutable());
        $this->getEntityManager()->persist($safe);
    }

    /**
     * @return QueryBuilder
     */
    public function findMissingSafesForCurrentMonth(): QueryBuilder
    {
        return $this->createQueryBuilder('b')
            ->andWhere('MONTH(b.created_at) = :month')
            ->setParameter('month', (new DateTimeImmutable())->format('m'));
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
