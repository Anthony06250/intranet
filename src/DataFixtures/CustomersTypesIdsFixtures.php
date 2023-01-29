<?php

namespace App\DataFixtures;

use App\Entity\CustomersTypesIds;
use DateTimeImmutable;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class CustomersTypesIdsFixtures extends Fixture
{
    /**
     * @var array|string[]
     */
    public const TYPES_ID = [
        'Carte Nationale d\'Identié (CNI)',
        'Passeport',
        'Titre de séjour'
    ];

    /**
     * @param ObjectManager $manager
     * @return void
     */
    public function load(ObjectManager $manager): void
    {
        $count = 1;

        foreach (self::TYPES_ID as $customersTypesid) {
            $object = new CustomersTypesIds();

            $object->setLabel($customersTypesid)
                ->setCreatedAt(new DateTimeImmutable())
                ->setUpdatedAt(new DateTimeImmutable());
            $manager->persist($object);
            $this->addReference('customersTypesId-' . $count, $object);

            $count += 1;
        }

        $manager->flush();
    }
}