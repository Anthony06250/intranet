<?php

namespace App\DataFixtures;

use App\Entity\UsersPermissions;
use DateTimeImmutable;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class UsersPermissionsFixtures extends Fixture
{
    /**
     * @var string[]
     */
    private array $usersPermissions = [
        'ROLE_SUPER_ADMIN' => 'Administrateur',
        'ROLE_ADMIN' => 'Direction',
        'ROLE_MANAGER' => 'Manageur',
        'ROLE_BUYER' => 'Acheteur',
        'ROLE_JEWELER' => 'Bijoutière',
        'ROLE_SELLER' => 'Vendeur'
    ];

    /**
     * @param ObjectManager $manager
     * @return void
     */
    public function load(ObjectManager $manager): void
    {
        $count = 1;

        foreach ($this->usersPermissions as $key => $value) {
            $object = new UsersPermissions();

            $object->setLabel($value)
                ->setRole($key)
                ->setCreatedAt(new DateTimeImmutable())
                ->setUpdatedAt(new DateTimeImmutable());
            $manager->persist($object);
            $this->addReference('usersPermission-' . $count, $object);

            $count += 1;
        }

        $manager->flush();
    }
}
