<?php

namespace App\DataFixtures;

use App\Controller\Admin\ControlsCrudController;
use App\Entity\Controls;
use DateTimeImmutable;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;

class ControlsFixtures extends Fixture implements DependentFixtureInterface
{
    /**
     * @param ObjectManager $manager
     * @return void
     */
    public function load(ObjectManager $manager): void
    {
        $dateInterval = 0;

        for ($count = 1; $count <= ControlsCrudController::MAX_RESULTS_REQUEST / 6; $count++) {
            for ($store = 1; $store <= 3; $store++) {
                for ($counter = 1; $counter <= 2; $counter++) {
                    for ($period = 1; $period <= 2; $period++) {
                        $manager->persist($this->loadControl($store, $counter, $period, $dateInterval));
                    }
                }
            }
            $dateInterval++;
        }

        $manager->flush();
    }

    /**
     * @param int $store
     * @param int $controlsCounter
     * @param int $controlsPeriod
     * @param int $dateInterval
     * @return Controls
     */
    private function loadControl(int $store, int $controlsCounter, int $controlsPeriod, int $dateInterval): Controls
    {
        $counter = $this->getReference('controlsCounter-' . $controlsCounter);
        $turnover = $this->rand_float(400, 1500) * 100;
        $cashFund = $counter->getCashFund();
        $currencies = $this->getCurrencies();
        $result = $this->getResult($currencies) * 100;
        $error = $counter->isReverse() !== 0 ? $result - ($turnover + $cashFund) : $result + ($turnover - $cashFund);
        $object = new Controls();

        return $object->setUser($this->getReference('user-' . mt_rand(1, count(UsersFixtures::USERS))))
            ->setStore($this->getReference('store-' . $store))
            ->setCounter($counter)
            ->setPeriod($this->getReference('controlsPeriod-' . $controlsPeriod))
            ->setTurnover($turnover)
            ->setCashFund($cashFund)
            ->setOneCent($currencies['one_cent'])
            ->setTwentyCents($currencies['two_cents'])
            ->setFiveCents($currencies['five_cents'])
            ->setTenCents($currencies['ten_cents'])
            ->setTwentyCents($currencies['twenty_cents'])
            ->setFiftyCents($currencies['fifty_cents'])
            ->setOneEuro($currencies['one_euros'])
            ->setTwoEuros($currencies['two_euros'])
            ->setFiveEuros($currencies['five_euros'])
            ->setTenEuros($currencies['ten_euros'])
            ->setTwentyEuros($currencies['twenty_euros'])
            ->setFiftyEuros($currencies['fifty_euros'])
            ->setOneHundredEuros($currencies['one_hundred_euros'])
            ->setTwoHundredEuros($currencies['two_hundred_euros'])
            ->setFiveHundredEuros($currencies['five_hundred_euros'])
            ->setResult($result)
            ->setError($error)
            ->setCreatedAt((new DateTimeImmutable())->modify('-' . $dateInterval . ' days'))
            ->setUpdatedAt((new DateTimeImmutable())->modify('-' . $dateInterval . ' days'));
    }

    /**
     * @return array
     */
    private function getCurrencies(): array
    {
        return [
            'one_cent' => mt_rand(0, 40),
            'two_cents' => mt_rand(0, 40),
            'five_cents' => mt_rand(0, 40),
            'ten_cents' => mt_rand(0, 40),
            'twenty_cents' => mt_rand(0, 40),
            'fifty_cents' => mt_rand(0, 40),
            'one_euros' => mt_rand(0, 40),
            'two_euros' => mt_rand(0, 40),
            'five_euros' => mt_rand(0, 40),
            'ten_euros' => mt_rand(0, 40),
            'twenty_euros' => mt_rand(0, 40),
            'fifty_euros' => mt_rand(0, 40),
            'one_hundred_euros' => mt_rand(0, 5),
            'two_hundred_euros' => mt_rand(0, 2),
            'five_hundred_euros' => mt_rand(0, 1)
        ];
    }

    /**
     * @param array $currencies
     * @return float
     */
    private function getResult(array $currencies): float
    {
        return ($currencies['one_cent'] * 0.01)
            + ($currencies['two_cents'] * 0.02)
            + ($currencies['five_cents'] * 0.05)
            + ($currencies['ten_cents'] * 0.1)
            + ($currencies['twenty_cents'] * 0.2)
            + ($currencies['fifty_cents'] * 0.5)
            + ($currencies['one_euros'] * 1)
            + ($currencies['two_euros'] * 2)
            + ($currencies['five_euros'] * 5)
            + ($currencies['ten_euros'] * 10)
            + ($currencies['twenty_euros'] * 20)
            + ($currencies['fifty_euros'] * 50)
            + ($currencies['one_hundred_euros'] * 100)
            + ($currencies['two_hundred_euros'] * 200)
            + ($currencies['five_hundred_euros'] * 500);
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
            ControlsCountersFixtures::class,
            ControlsPeriodsFixtures::class
        ];
    }
}
