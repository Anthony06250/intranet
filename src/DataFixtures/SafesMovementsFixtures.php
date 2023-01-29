<?php

namespace App\DataFixtures;

use App\Controller\Admin\SafesMovementsCrudController;
use App\Entity\SafesMovements;
use DateTimeImmutable;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;
use Exception;
use Faker;

class SafesMovementsFixtures extends Fixture implements DependentFixtureInterface
{
    /**
     * @param ObjectManager $manager
     * @return void
     * @throws Exception
     */
    public function load(ObjectManager $manager): void
    {
        $faker = Faker\Factory::create('fr_FR');

        for ($count = 2; $count <= SafesMovementsCrudController::MAX_RESULTS_REQUEST / 4; $count++) {
            $object = new SafesMovements();
            $amount = $this->rand_float(50, 250) * 100;
            $date = new DateTimeImmutable($faker->dateTimeBetween('-50 days')->format('Y-m-d H:i:s'));

            $object->setUser($this->getReference('user-' . mt_rand(1, count(UsersFixtures::USERS))))
                ->setStore($this->getReference('store-' . mt_rand(1, count(StoresFixtures::STORES))))
                ->setSafesMovementsType($this->getReference('SafesMovementsType-' . mt_rand(1, count(SafesMovementsTypesFixtures::TYPES))))
                ->setAmount($faker->boolean() ? $amount : $amount * -1)
                ->setComments($faker->realText())
                ->setCreatedAt($date)
                ->setUpdatedAt($date);
            $manager->persist($object);
        }

        $manager->flush();
    }

    /**
     * @param int $start
     * @param int $end
     * @return float
     */
    private function rand_float(int $start = 0, int $end = 1): float
    {
        if ($start > $end) {
            return false;
        }

        return round(mt_rand($start * 1000000, $end * 1000000) / 1000000, 2);
    }

    /**
     * @return string[]
     */
    public function getDependencies(): array
    {
        return [
            UsersFixtures::class,
            StoresFixtures::class,
            SafesMovementsTypesFixtures::class
        ];
    }
}