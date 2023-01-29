<?php

namespace App\DataFixtures;

use App\Entity\UsersCivilities;
use DateTimeImmutable;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class UsersCivilitiesFixtures extends Fixture
{
    /**
     * @var array|string[]
     */
    public const CIVILITIES = [
        'Monsieur',
        'Madame'
    ];

    /**
     * @param ObjectManager $manager
     * @return void
     */
    public function load(ObjectManager $manager): void
    {
        $count = 1;

        foreach (self::CIVILITIES as $usersCivility) {
            $object = new UsersCivilities();

            $object->setLabel($usersCivility)
                ->setCreatedAt(new DateTimeImmutable())
                ->setUpdatedAt(new DateTimeImmutable());
            $manager->persist($object);
            $this->addReference('usersCivility-' . $count, $object);

            $count += 1;
        }

        $manager->flush();
    }
}
