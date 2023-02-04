<?php

namespace App\DataFixtures;

use App\Entity\AdvancesPaymentsMethods;
use DateTimeImmutable;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class AdvancesPaymentsMethodsFixtures extends Fixture
{
    public const PAYMENTS_METHODS = [
        'Carte bancaire',
        'Éspèces',
        'Paypal',
        'Bon d\'achat',
        'Bon d\'avoir'
    ];

    /**
     * @param ObjectManager $manager
     * @return void
     */
    public function load(ObjectManager $manager): void
    {
        $count = 1;

        foreach (self::PAYMENTS_METHODS as $paymentMethods) {
            $object = new AdvancesPaymentsMethods();

            $object->setLabel($paymentMethods)
                ->setCreatedAt(new DateTimeImmutable())
                ->setUpdatedAt(new DateTimeImmutable());
            $manager->persist($object);
            $this->addReference('advancesPaymentMethods-' . $count, $object);

            $count += 1;
        }

        $manager->flush();
    }
}
