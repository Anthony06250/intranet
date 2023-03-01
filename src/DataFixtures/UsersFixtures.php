<?php

namespace App\DataFixtures;

use App\Entity\Users;
use DateTime;
use DateTimeImmutable;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;
use Exception;
use libphonenumber\PhoneNumberUtil;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class UsersFixtures extends Fixture implements DependentFixtureInterface
{
    public const USERS = [
        [
            'username' => 'anthony',
            'civility' => '1',
            'firstname' => 'anthony',
            'lastname' => 'duret',
            'password' => 'Antoine2019',
            'permission' => '1',
            'stores' => [
                '1',
                '2',
                '3'
            ],
            'birthday' => '1987-08-07',
            'hiring' => '2014-09-01',
            'email' => 'anthony.duret@outlook.fr',
            'phone' => '0618824312'
        ],
        [
            'username' => 'ugo',
            'civility' => '1',
            'firstname' => 'ugo',
            'lastname' => 'duret',
            'password' => 'test',
            'permission' => '2',
            'stores' => [
                '1',
                '2',
                '3'
            ],
            'birthday' => '1991-07-30',
            'hiring' => '2014-09-01',
            'email' => 'ugo06@hotmail.fr',
            'phone' => '0622621578'
        ],
        [
            'username' => 'marie',
            'civility' => '2',
            'firstname' => 'marie',
            'lastname' => 'duret',
            'password' => 'test',
            'permission' => '2',
            'stores' => [
                '1',
                '2',
                '3'
            ],
            'birthday' => '1958-04-21',
            'hiring' => '2014-09-01',
            'email' => 'duret_marie@hotmail.fr',
            'phone' => '0620461018'
        ],
        [
            'username' => 'remy',
            'civility' => '1',
            'firstname' => 'rémy',
            'lastname' => 'peraire',
            'password' => 'test',
            'permission' => '3',
            'stores' => [
                '1'
            ],
            'birthday' => '1996-04-25',
            'hiring' => '2017-10-02',
            'email' => 'remy.peraire@hotmail.fr',
            'phone' => '0778391677'
        ],
        [
            'username' => 'benjamin',
            'civility' => '1',
            'firstname' => 'benjamin',
            'lastname' => 'bouguen',
            'password' => 'test',
            'permission' => '3',
            'stores' => [
                '2'
            ],
            'birthday' => '1983-05-17',
            'hiring' => '2020-02-01',
            'email' => 'benjmarie1701@yahoo.fr',
            'phone' => '0769872261'
        ],
        [
            'username' => 'nicolas',
            'civility' => '1',
            'firstname' => 'nicolas',
            'lastname' => 'peraire',
            'password' => 'test',
            'permission' => '3',
            'stores' => [
                '3'
            ],
            'birthday' => '1996-04-25',
            'hiring' => '2021-06-14',
            'email' => 'nicolasperaire@icloud.com',
            'phone' => '0618236525'
        ],
        [
            'username' => 'lea',
            'civility' => '2',
            'firstname' => 'léa',
            'lastname' => 'pietri',
            'password' => 'test',
            'permission' => '5',
            'stores' => [
                '1'
            ],
            'birthday' => '1996-09-11',
            'hiring' => '2015-10-12',
            'email' => 'lea.pietri@hotmail.fr',
            'phone' => '0629173394'
        ],
        [
            'username' => 'jimmy',
            'civility' => '1',
            'firstname' => 'jimmy',
            'lastname' => 'grattepanche',
            'password' => 'test',
            'permission' => '4',
            'stores' => [
                '2'
            ],
            'birthday' => '1991-05-07',
            'hiring' => '2022-03-01',
            'email' => 'grattepanche.jimmy@gmail.com',
            'phone' => '0780458579'
        ],
        [
            'username' => 'antho',
            'civility' => '1',
            'firstname' => 'anthony',
            'lastname' => 'minidre',
            'password' => 'test',
            'permission' => '4',
            'stores' => [
                '3'
            ],
            'birthday' => '1991-10-01',
            'hiring' => '2015-05-11',
            'email' => 'anthony.minidre@gmail.com',
            'phone' => '0699520517'
        ],
        [
            'username' => 'toty',
            'civility' => '1',
            'firstname' => 'anthony',
            'lastname' => 'vert-belaiche',
            'password' => 'test',
            'permission' => '6',
            'stores' => [
                '1',
                '2',
                '3'
            ],
            'birthday' => '2001-08-08',
            'hiring' => '2022-09-15',
            'email' => 'anthohome50@gmail.com',
            'phone' => '0615094469'
        ]
    ];

    /**
     * @param UserPasswordHasherInterface $hasher
     * @param PhoneNumberUtil $phoneNumberUtil
     */
    public function __construct(private readonly UserPasswordHasherInterface $hasher,
                                private readonly PhoneNumberUtil $phoneNumberUtil)
    {
    }

    /**
     * @param ObjectManager $manager
     * @return void
     * @throws Exception
     */
    public function load(ObjectManager $manager): void
    {
        foreach (self::USERS as $key => $user) {
            $object = new Users();

            $object->setUsername($user['username'])
                ->setCivility($this->getReference('usersCivility-' . $user['civility']))
                ->setFirstname($user['firstname'])
                ->setLastname($user['lastname'])
                ->setPassword($this->hasher->hashPassword($object, $user['password']))
                ->setPermission($this->getReference('usersPermission-' . $user['permission']))
                ->setBirthdayDate($user['birthday'] ? new DateTime($user['birthday']) : null)
                ->setHiringDate($user['hiring'] ? new DateTime($user['hiring']) : null)
                ->setEmail($user['email'])
                ->setPhone($user['phone'] ? $this->phoneNumberUtil->parse($user['phone'], 'FR') : null)
                ->setActive(true)
                ->setCreatedAt(new DateTimeImmutable())
                ->setUpdatedAt(new DateTimeImmutable());

            foreach ($user['stores'] as $store) {
                $object->addStore($this->getReference('store-' . $store));
            }

            $manager->persist($object);
            $this->addReference('user-' . ($key + 1), $object);
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
            UsersPermissionsFixtures::class,
            StoresFixtures::class
        ];
    }
}
