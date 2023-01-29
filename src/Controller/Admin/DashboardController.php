<?php

namespace App\Controller\Admin;

use App\Entity\Buybacks;
use App\Entity\Controls;
use App\Entity\ControlsCounters;
use App\Entity\ControlsPeriods;
use App\Entity\Customers;
use App\Entity\CustomersTypesIds;
use App\Entity\Safes;
use App\Entity\SafesControls;
use App\Entity\SafesMovements;
use App\Entity\SafesMovementsTypes;
use App\Entity\UsersCivilities;
use App\Entity\UsersPermissions;
use App\Entity\Stores;
use App\Entity\Users;
use App\Repository\BuybacksRepository;
use App\Repository\ControlsRepository;
use App\Repository\CustomersRepository;
use App\Repository\SafesControlsRepository;
use App\Repository\SafesMovementsRepository;
use App\Repository\SafesRepository;
use App\Repository\StoresRepository;
use App\Repository\UsersRepository;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\ORM\NoResultException;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Config\Dashboard;
use EasyCorp\Bundle\EasyAdminBundle\Config\Menu\CrudMenuItem;
use EasyCorp\Bundle\EasyAdminBundle\Config\Menu\SubMenuItem;
use EasyCorp\Bundle\EasyAdminBundle\Config\MenuItem;
use EasyCorp\Bundle\EasyAdminBundle\Config\UserMenu;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractDashboardController;
use Exception;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\User\UserInterface;

class DashboardController extends AbstractDashboardController
{
    /**
     * Dashboard page settings
     */
    public const DAYS_UNTIL_BIRTHDAYS = 90;

    /**
     * @param UsersRepository $usersRepository
     * @param CustomersRepository $customersRepository
     * @param StoresRepository $storesRepository
     * @param ControlsRepository $controlsRepository
     * @param SafesRepository $safesRepository
     * @param SafesMovementsRepository $safesMovementsRepository
     * @param SafesControlsRepository $safesControlsRepository
     * @param BuybacksRepository $buybacksRepository
     */
    public function __construct(private readonly UsersRepository $usersRepository,
                                private readonly CustomersRepository $customersRepository,
                                private readonly StoresRepository $storesRepository,
                                private readonly ControlsRepository $controlsRepository,
                                private readonly SafesRepository $safesRepository,
                                private readonly SafesMovementsRepository $safesMovementsRepository,
                                private readonly SafesControlsRepository $safesControlsRepository,
                                private readonly BuybacksRepository $buybacksRepository)
    {
    }

    /**
     * @return Response
     */
    #[Route('/admin', name: 'admin')]
    public function index(): Response
    {
        try {
            $countUsers = $this->usersRepository->countUsers();
            $countCustomers = $this->customersRepository->countCustomers();
            $countStores = $this->storesRepository->countStores();
            $countControls = $this->controlsRepository->countControls();
            $countSafes = $this->safesRepository->countSafes();
            $countSafesMovements = $this->safesMovementsRepository->countSafesMovements();
            $countSafesControls = $this->safesControlsRepository->countSafesControls();
            $countBuybacks = $this->buybacksRepository->countBuybacks();
            $nextBirthdays = $this->usersRepository->findNextBirthday(self::DAYS_UNTIL_BIRTHDAYS);
        } catch (NoResultException|NonUniqueResultException|Exception) {
        }

        return $this->render('bundles/EasyAdminBundle/page/dashboard.html.twig', [
            'count_users' => $countUsers ?? 0,
            'count_customers' => $countCustomers ?? 0,
            'count_stores' => $countStores ?? 0,
            'count_controls' => $countControls ?? 0,
            'count_safes' => $countSafes ?? 0,
            'count_safesMovements' => $countSafesMovements ?? 0,
            'count_safesControls' => $countSafesControls ?? 0,
            'count_buybacks' => $countBuybacks ?? 0,
            'next_birthdays' => $nextBirthdays ?? null,
            'days_until_birthdays' => self::DAYS_UNTIL_BIRTHDAYS
        ]);
    }

    /**
     * @return Dashboard
     */
    public function configureDashboard(): Dashboard
    {
        return Dashboard::new()
            ->SetFaviconPath('assets/images/favicon.png');
    }

    /**
     * @return iterable
     */
    public function configureMenuItems(): iterable
    {
        // Dashboard menu
        yield MenuItem::linkToDashboard('Menu.Dashboard', 'uil-dashboard');
        // Users menu
        yield $this->configureUsersMenuItem();
        // Customers menu
        yield $this->configureCustomersMenuItem();
        // Stores menu
        yield $this->configureStoresMenuItem();
        // Controls menu
        yield $this->configureControlsMenuItem();
        // Safes menu
        yield $this->configureSafesMenuItem();
        // Buybacks menu
        yield $this->configureBuybacksMenuItem();
    }

    /**
     * @return CrudMenuItem|SubMenuItem
     */
    private function configureUsersMenuItem(): CrudMenuItem|SubMenuItem
    {
        if ($this->isGranted(UsersCrudController::ROLE_NEW)) {
            return MenuItem::subMenu('Menu.Users', 'uil-users-alt')
                ->setSubItems([
                    MenuItem::linkToCrud('Users.List of users', null, Users::class)
                        ->setAction(Crud::PAGE_INDEX),
                    MenuItem::section(),
                    MenuItem::linkToCrud('Users.Create user', null, Users::class)
                        ->setAction(Crud::PAGE_NEW),
                    MenuItem::section()
                        ->setPermission(UsersCivilitiesCrudController::ROLE_INDEX),
                    MenuItem::linkToCrud('UsersCivilities.List of users civilities', null, UsersCivilities::class)
                        ->setAction(Crud::PAGE_INDEX)
                        ->setPermission(UsersCivilitiesCrudController::ROLE_INDEX),
                    MenuItem::linkToCrud('UsersPermissions.List of users permissions', null, UsersPermissions::class)
                        ->setAction(Crud::PAGE_INDEX)
                        ->setPermission(UsersPermissionsCrudController::ROLE_INDEX)
                ]);
        }

        return MenuItem::linkToCrud('Menu.Users', 'uil-users-alt', Users::class)
            ->setAction(Crud::PAGE_INDEX);
    }

    /**
     * @return CrudMenuItem|SubMenuItem
     */
    private function configureCustomersMenuItem(): CrudMenuItem|SubMenuItem
    {
        if ($this->isGranted(CustomersCrudController::ROLE_NEW)) {
            return MenuItem::subMenu('Menu.Customers', 'uil-raddit-alien-alt')->setSubItems([
                MenuItem::linkToCrud('Customers.List of customers', null, Customers::class)
                    ->setAction(Crud::PAGE_INDEX),
                MenuItem::section(),
                MenuItem::linkToCrud('Customers.Create customer', null, Customers::class)
                    ->setAction(Crud::PAGE_NEW),
                MenuItem::section()
                ->setPermission(CustomersTypesIdsCrudController::ROLE_INDEX),
                MenuItem::linkToCrud('CustomersTypesIds.List of customers types ids', null, CustomersTypesIds::class)
                    ->setAction(Crud::PAGE_INDEX)
                    ->setPermission(CustomersTypesIdsCrudController::ROLE_INDEX)
            ]);
        }

        return MenuItem::linkToCrud('Menu.Customers', 'uil-raddit-alien-alt', Customers::class)
            ->setAction(Crud::PAGE_INDEX);
    }

    /**
     * @return CrudMenuItem|SubMenuItem
     */
    private function configureStoresMenuItem(): CrudMenuItem|SubMenuItem
    {
        if ($this->isGranted(StoresCrudController::ROLE_NEW)) {
            return MenuItem::subMenu('Menu.Stores', 'uil-store')->setSubItems([
                MenuItem::linkToCrud('Stores.List of stores', null, Stores::class)
                    ->setAction(Crud::PAGE_INDEX),
                MenuItem::section(),
                MenuItem::linkToCrud('Stores.Create store', null, Stores::class)
                    ->setAction(Crud::PAGE_NEW)
            ]);
        }

        return MenuItem::linkToCrud('Menu.Stores', 'uil-store', Stores::class)
            ->setAction(Crud::PAGE_INDEX);
    }

    /**
     * @return CrudMenuItem|SubMenuItem
     */
    private function configureControlsMenuItem(): CrudMenuItem|SubMenuItem
    {
        if ($this->isGranted(ControlsCrudController::ROLE_NEW_SELL)) {
            return MenuItem::subMenu('Menu.Controls', 'uil-file-check-alt')->setSubItems([
                MenuItem::linkToCrud('Controls.List of controls', null, Controls::class)
                    ->setAction(Crud::PAGE_INDEX),
                MenuItem::section(),
                MenuItem::linkToCrud('Controls.Create sell morning control', null, Controls::class)
                    ->setQueryParameter('controlsCounter', '1')
                    ->setQueryParameter('controlsPeriod', '1')
                    ->setAction(Crud::PAGE_NEW),
                MenuItem::linkToCrud('Controls.Create sell evening control', null, Controls::class)
                    ->setQueryParameter('controlsCounter', '1')
                    ->setQueryParameter('controlsPeriod', '2')
                    ->setAction(Crud::PAGE_NEW),
                MenuItem::section()
                    ->setPermission(ControlsCrudController::ROLE_NEW_BUY),
                MenuItem::linkToCrud('Controls.Create buy morning control', null, Controls::class)
                    ->setQueryParameter('controlsCounter', '2')
                    ->setQueryParameter('controlsPeriod', '1')
                    ->setAction(Crud::PAGE_NEW)
                    ->setPermission(ControlsCrudController::ROLE_NEW_BUY),
                MenuItem::linkToCrud('Controls.Create buy evening control', null, Controls::class)
                    ->setQueryParameter('controlsCounter', '2')
                    ->setQueryParameter('controlsPeriod', '2')
                    ->setAction(Crud::PAGE_NEW)
                    ->setPermission(ControlsCrudController::ROLE_NEW_BUY),
                MenuItem::section()
                    ->setPermission(ControlsCountersCrudController::ROLE_INDEX),
                MenuItem::linkToCrud('ControlsCounters.List of controlsCounters', null, ControlsCounters::class)
                    ->setAction(Crud::PAGE_INDEX)
                    ->setPermission(ControlsCountersCrudController::ROLE_INDEX),
                MenuItem::linkToCrud('ControlsPeriods.List of controlsPeriods', null, ControlsPeriods::class)
                    ->setAction(Crud::PAGE_INDEX)
                    ->setPermission(ControlsPeriodsCrudController::ROLE_INDEX)
            ]);
        }

        return MenuItem::linkToCrud('Menu.Controls', 'uil-file-check-alt', Controls::class)
            ->setAction(Crud::PAGE_INDEX);
    }

    /**
     * @return CrudMenuItem|SubMenuItem
     */
    private function configureSafesMenuItem(): CrudMenuItem|SubMenuItem
    {
        if ($this->isGranted(SafesCrudController::ROLE_NEW)) {
            return MenuItem::subMenu('Menu.Safes', 'uil-shield-check')->setSubItems([
                MenuItem::linkToCrud('Safes.List of safes', null, Safes::class)
                    ->setAction(Crud::PAGE_INDEX),
                MenuItem::section(),
                MenuItem::linkToCrud('Safes.Create safe', null, Safes::class)
                    ->setAction(Crud::PAGE_NEW),
                MenuItem::section()
                    ->setPermission(SafesMovementsCrudController::ROLE_INDEX),
                MenuItem::linkToCrud('SafesMovements.List of safes movements', null, SafesMovements::class)
                    ->setAction(Crud::PAGE_INDEX)
                    ->setPermission(SafesMovementsCrudController::ROLE_INDEX),
                MenuItem::linkToCrud('SafesMovementsTypes.List of safes movements types', null, SafesMovementsTypes::class)
                    ->setAction(Crud::PAGE_INDEX)
                    ->setPermission(SafesMovementsTypesCrudController::ROLE_INDEX),
                MenuItem::linkToCrud('SafesControls.List of safes controls', null, SafesControls::class)
                    ->setAction(Crud::PAGE_INDEX)
                    ->setPermission(SafesControlsCrudController::ROLE_INDEX),
            ]);
        }

        return MenuItem::linkToCrud('Menu.Safes', 'uil-shield-check', Safes::class)
            ->setAction(Crud::PAGE_INDEX);
    }

    /**
     * @return CrudMenuItem|SubMenuItem
     */
    private function configureBuybacksMenuItem(): CrudMenuItem|SubMenuItem
    {
        if ($this->isGranted(BuybacksCrudController::ROLE_NEW)) {
            return MenuItem::subMenu('Menu.Buybacks', 'uil-exchange-alt')->setSubItems([
                MenuItem::linkToCrud('Buybacks.List of buybacks', null, Buybacks::class)
                    ->setAction(Crud::PAGE_INDEX),
                MenuItem::section(),
                MenuItem::linkToCrud('Buybacks.Create buyback', null, Buybacks::class)
                    ->setAction(Crud::PAGE_NEW)
            ]);
        }

        return MenuItem::linkToCrud('Menu.Buybacks', 'uil-exchange-alt', Buybacks::class)
            ->setAction(Crud::PAGE_INDEX);
    }

    /**
     * @param UserInterface $user
     * @return UserMenu
     */
    public function configureUserMenu(UserInterface $user): UserMenu
    {
        return UserMenu::new()
            ->setName((string) $user)
            ->setGravatarEmail($user->getEmail() ?? 'email@example.com')
            ->addMenuItems([
                MenuItem::section('Menu.Welcome !'),
                MenuItem::linkToCrud('Menu.My account', 'mdi mdi-account-circle', Users::class)
                    ->setAction(Crud::PAGE_DETAIL)
                    ->setEntityId($user->getId()),
                MenuItem::linkToUrl('Menu.Support', 'mdi mdi-lifebuoy', 'mailto:anthony.duret@outlook.fr'),
                MenuItem::section(),
                MenuItem::linkToLogout('Menu.Logout', 'mdi mdi-logout')
            ]);
    }
}
