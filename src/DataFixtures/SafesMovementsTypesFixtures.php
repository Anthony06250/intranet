<?php

namespace App\DataFixtures;

use App\Entity\SafesMovementsTypes;
use DateTimeImmutable;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class SafesMovementsTypesFixtures extends Fixture
{
    /**
     * @var array|string[]
     */
    public const TYPES = [
        'Banque',
        'Prime',
        'Divers'
    ];

    /**
     * @param ObjectManager $manager
     * @return void
     */
    public function load(ObjectManager $manager): void
    {
        $count = 1;

        foreach (self::TYPES as $safesMovementsType) {
            $object = new SafesMovementsTypes();

            $object->setLabel($safesMovementsType)
                ->setCreatedAt(new DateTimeImmutable())
                ->setUpdatedAt(new DateTimeImmutable());
            $manager->persist($object);
            $this->addReference('SafesMovementsType-' . $count, $object);

            $count += 1;
        }

        $manager->flush();
    }
}