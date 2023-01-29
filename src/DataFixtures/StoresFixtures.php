<?php

namespace App\DataFixtures;

use App\Entity\Stores;
use DateTimeImmutable;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use libphonenumber\NumberParseException;
use libphonenumber\PhoneNumberUtil;

class StoresFixtures extends Fixture
{
    public const STORES = [
        [
            'address' => '59 route de Cannes',
            'additional_address' => 'Centre commercial AXE85',
            'city' => 'grasse',
            'zipcode' => '06130',
            'phone' => '0951313127',
            'email' => 'grasse@axe-cash.fr',
            'crn' => '802 369 520'
        ],
        [
            'address' => '225 avenue Saint-Exupéry',
            'additional_address' => 'Zones des Tourrades',
            'city' => 'mandelieu',
            'zipcode' => '06210',
            'phone' => '0972851320',
            'email' => 'mandelieu@axe-cash.fr',
            'crn' => '802 369 520'
        ],
        [
            'address' => '12 rue Vénizélos',
            'additional_address' => '',
            'city' => 'cannes',
            'zipcode' => '06400',
            'phone' => '0961656134',
            'email' => 'cannes@axe-cash.fr',
            'crn' => '802 369 520'
        ]
    ];

    /**
     * @param PhoneNumberUtil $phoneNumberUtil
     */
    public function __construct(private readonly PhoneNumberUtil $phoneNumberUtil)
    {
    }

    /**
     * @param ObjectManager $manager
     * @return void
     * @throws NumberParseException
     */
    public function load(ObjectManager $manager): void
    {
        foreach (self::STORES as $key => $store) {
            $object = new Stores();

            $object->setAddress($store['address'])
                ->setAdditionalAddress($store['additional_address'])
                ->setCity($store['city'])
                ->setZipcode($store['zipcode'])
                ->setPhone($store['phone'] ? $this->phoneNumberUtil->parse($store['phone'], 'FR') : null)
                ->setEmail($store['email'])
                ->setCommercialRegisterNumber($store['crn'])
                ->setCreatedAt(new DateTimeImmutable())
                ->setUpdatedAt(new DateTimeImmutable());
            $manager->persist($object);
            $this->addReference('store-' . ($key + 1), $object);
        }

        $manager->flush();
    }
}
