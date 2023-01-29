<?php

namespace App\DataFixtures;

use App\Entity\ControlsCounters;
use DateTimeImmutable;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class ControlsCountersFixtures extends Fixture
{
    /**
     * @var array|string[]
     */
    private array $controlsCounters = [
        [
            'label' => 'Ventes',
            'cash_fund' => 40000,
            'reverse' => false
        ],
        [
            'label' => 'Achats',
            'cash_fund' => 100000,
            'reverse' => true
        ]
    ];

    /**
     * @param ObjectManager $manager
     * @return void
     */
    public function load(ObjectManager $manager): void
    {
        $count = 1;

        foreach ($this->controlsCounters as $controlsCounter) {
            $object = new ControlsCounters();

            $object->setLabel($controlsCounter['label'])
                ->setCashFund($controlsCounter['cash_fund'])
                ->setReverse($controlsCounter['reverse'])
                ->setCreatedAt(new DateTimeImmutable())
                ->setUpdatedAt(new DateTimeImmutable());
            $manager->persist($object);
            $this->addReference('controlsCounter-' . $count, $object);

            $count += 1;
        }

        $manager->flush();
    }
}
