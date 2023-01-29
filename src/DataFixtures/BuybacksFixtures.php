<?php

namespace App\DataFixtures;

use App\Controller\Admin\BuybacksCrudController;
use App\Controller\Admin\CustomersCrudController;
use App\DBAL\Types\BuybacksStatusesType;
use App\Entity\Buybacks;
use DateTimeImmutable;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;
use Exception;
use Faker;

class BuybacksFixtures extends Fixture implements DependentFixtureInterface
{
    /**
     * @param ObjectManager $manager
     * @return void
     * @throws Exception
     */
    public function load(ObjectManager $manager): void
    {
        $faker = Faker\Factory::create('fr_FR');

        for ($count = 1; $count <= BuybacksCrudController::MAX_RESULTS_REQUEST / 2; $count++) {
            $object = new Buybacks();
            $startingPrice = $this->rand_float(100, 1000) * 100;
            $percent = $this->rand_float(0, 100) / 100;
            $increasedPrice = $startingPrice + ($startingPrice * $percent);
            $duration = mt_rand(1, 28);

            $object->setUser($this->getReference('user-' . mt_rand(1, count(UsersFixtures::USERS))))
                ->setStore($this->getReference('store-' . mt_rand(1, count(StoresFixtures::STORES))))
                ->setProduct($faker->realText(50))
                ->setStatus(BuybacksStatusesType::TYPES[mt_rand(0, 2)])
                ->setStartingPrice($startingPrice)
                ->setIncreasedPercent($percent)
                ->setIncreasedPrice($increasedPrice)
                ->setCustomer($this->getReference('customer-' . mt_rand(1, CustomersCrudController::MAX_RESULTS_REQUEST)))
                ->setComments($faker->realText())
                ->setCreatedAt(new DateTimeImmutable())
                ->setDuration($duration)
                ->setDueAt((new DateTimeImmutable())->modify('+' . $duration . ' days'))
                ->setUpdatedAt(new DateTimeImmutable());
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
            CustomersFixtures::class
        ];
    }
}
