<?php

namespace App\DataFixtures;

use App\Entity\InvoicesTaxesRates;
use DateTimeImmutable;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class InvoicesTaxesRatesFixtures extends Fixture
{
    public const TAXES_RATES = [
        [
            'label' => 'Pas de T.V.A.',
            'rate' => 0
        ],
        [
            'label' => 'T.V.A. à 20%',
            'rate' => 20
        ]
    ];

    /**
     * @param ObjectManager $manager
     * @return void
     */
    public function load(ObjectManager $manager): void
    {
        $count = 1;

        foreach (self::TAXES_RATES as $taxesRate) {
            $object = new InvoicesTaxesRates();

            $object->setLabel($taxesRate['label'])
                ->setRate($taxesRate['rate'] / 100)
                ->setCreatedAt(new DateTimeImmutable())
                ->setUpdatedAt(new DateTimeImmutable());
            $manager->persist($object);
            $this->addReference('taxesRate-' . $count, $object);

            $count += 1;
        }

        $manager->flush();
    }
}