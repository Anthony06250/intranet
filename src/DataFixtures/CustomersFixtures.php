<?php

namespace App\DataFixtures;

use App\Controller\Admin\CustomersCrudController;
use App\Entity\Customers;
use DateTimeImmutable;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;
use Faker;
use libphonenumber\NumberParseException;
use libphonenumber\PhoneNumberUtil;

class CustomersFixtures extends Fixture implements DependentFixtureInterface
{
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
        $faker = Faker\Factory::create('fr_FR');

        for ($count = 1; $count <= CustomersCrudController::MAX_RESULTS_REQUEST; $count++) {
            $object = new Customers();

            $object->setCivility($this->getReference('usersCivility-' . $faker->numberBetween(1, count(UsersCivilitiesFixtures::CIVILITIES))))
                ->setFirstname($faker->firstName)
                ->setLastname($faker->lastName)
                ->setBirthdayDate($faker->dateTime)
                ->setCustomersTypesId($this->getReference('customersTypesId-' . $faker->numberBetween(1, count(CustomersTypesIdsFixtures::TYPES_ID))))
                ->setIdNumber($faker->postcode . $faker->postcode)
                ->setAddress($faker->address)
                ->setCity($faker->city)
                ->setZipcode($faker->postcode)
                ->setPhone($this->phoneNumberUtil->parse($faker->phoneNumber, 'FR'))
                ->setEmail($faker->email)
                ->setCreatedAt(new DateTimeImmutable())
                ->setUpdatedAt(new DateTimeImmutable());
            $manager->persist($object);
            $this->addReference('customer-' . $count, $object);
        }

        $manager->flush();
    }

    /**
     * @return string[]
     */
    public function getDependencies(): array
    {
        return [
            UsersCivilitiesFixtures::class,
            CustomersTypesIdsFixtures::class
        ];
    }
}