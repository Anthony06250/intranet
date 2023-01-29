<?php

namespace App\DataFixtures;

use App\Entity\ControlsPeriods;
use DateTimeImmutable;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class ControlsPeriodsFixtures extends Fixture
{
    /**
     * @var array|string[]
     */
    private array $controlsPeriods = [
        'Matin',
        'Soir'
    ];

    /**
     * @param ObjectManager $manager
     * @return void
     */
    public function load(ObjectManager $manager): void
    {
        $count = 1;

        foreach ($this->controlsPeriods as $controlsPeriod) {
            $object = new ControlsPeriods();

            $object->setLabel($controlsPeriod)
                ->setCreatedAt(new DateTimeImmutable())
                ->setUpdatedAt(new DateTimeImmutable());
            $manager->persist($object);
            $this->addReference('controlsPeriod-' . $count, $object);

            $count += 1;
        }

        $manager->flush();
    }
}
