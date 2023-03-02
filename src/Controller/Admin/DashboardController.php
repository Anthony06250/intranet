<?php

namespace App\Controller\Admin;

use App\Entity\AdvancesPayments;
use App\Entity\AdvancesPaymentsMethods;
use App\Entity\Buybacks;
use App\Entity\Controls;
use App\Entity\ControlsCounters;
use App\Entity\ControlsPeriods;
use App\Entity\Customers;
use App\Entity\CustomersTypesIds;
use App\Entity\DepositsSales;
use App\Entity\Invoices;
use App\Entity\InvoicesTaxesRates;
use App\Entity\Safes;
use App\Entity\SafesControls;
use App\Entity\SafesMovements;
use App\Entity\SafesMovementsTypes;
use App\Entity\UsersCivilities;
use App\Entity\UsersPermissions;
use App\Entity\Stores;
use App\Entity\Users;
use App\Repository\AdvancesPaymentsRepository;
use App\Repository\BuybacksRepository;
use App\Repository\ControlsRepository;
use App\Repository\CustomersRepository;
use App\Repository\DepositsSalesRepository;
use App\Repository\InvoicesRepository;
use App\Repository\SafesControlsRepository;
use App\Repository\SafesMovementsRepository;
use App\Repository\SafesRepository;
use App\Repository\StoresRepository;
use App\Repository\UsersRepository;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\ORM\NoResultException;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
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
    private array $todayControlsIds = [];

    /**
     * Dashboard page settings
     */
    public const DAYS_UNTIL_BIRTHDAYS = 60;

    /**
     * @param UsersRepository $usersRepository
     * @param CustomersRepository $customersRepository
     * @param StoresRepository $storesRepository
     * @param ControlsRepository $controlsRepository
     * @param SafesRepository $safesRepository
     * @param SafesMovementsRepository $safesMovementsRepository
     * @param SafesControlsRepository $safesControlsRepository
     * @param BuybacksRepository $buybacksRepository
     * @param DepositsSalesRepository $depositsSalesRepository
     * @param AdvancesPaymentsRepository $advancesPaymentsRepository
     * @param InvoicesRepository $invoicesRepository
     */
    public function __construct(private readonly UsersRepository $usersRepository,
                                private readonly CustomersRepository $customersRepository,
                                private readonly StoresRepository $storesRepository,
                                private readonly ControlsRepository $controlsRepository,
                                private readonly SafesRepository $safesRepository,
                                private readonly SafesMovementsRepository $safesMovementsRepository,
                                private readonly SafesControlsRepository $safesControlsRepository,
                                private readonly BuybacksRepository $buybacksRepository,
                                private readonly DepositsSalesRepository $depositsSalesRepository,
                                private readonly AdvancesPaymentsRepository $advancesPaymentsRepository,
                                private readonly InvoicesRepository $invoicesRepository)
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
            $nextBirthdays = $this->usersRepository->findNextBirthday(self::DAYS_UNTIL_BIRTHDAYS);
            $countCustomers = $this->customersRepository->countCustomers();
            $countStores = $this->storesRepository->countStores();
            $countControls = $this->controlsRepository->countControls();
            $countSafes = $this->safesRepository->countSafes();
            $countSafesMovements = $this->safesMovementsRepository->countSafesMovements();
            $countSafesControls = $this->safesControlsRepository->countSafesControls();
            $countBuybacks = $this->buybacksRepository->countBuybacks();
            $countDepositsSales = $this->depositsSalesRepository->countDepositsSales();
            $countAdvancesPayments = $this->advancesPaymentsRepository->countAdvancesPayments();
            $countInvoices = $this->invoicesRepository->countInvoices();
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
            'count_deposits_sales' => $countDepositsSales ?? 0,
            'count_advances_payments' => $countAdvancesPayments ?? 0,
            'count_invoices' => $countInvoices ?? 0,
            'birthday' => [
                'days_until_birthdays' => self::DAYS_UNTIL_BIRTHDAYS,
                'next_birthdays' => $nextBirthdays ?? null,
            ],
            'today_controls_ids' => $this->todayControlsIds
        ]);
    }

    /**
     * @return Dashboard
     */
    public function configureDashboard(): Dashboard
    {
        $this->getTodayControlsIds();

        return Dashboard::new()
            ->SetFaviconPath('build/images/favicon.png');
    }

    /**
     * @return void
     */
    private function getTodayControlsIds(): void
    {
        if (count($this->getUser()->getStores()) === 1) {
            $store = $this->getUser()->getStores()[0];
            $this->todayControlsIds['sell']['morning'] = $this->controlsRepository->findTodayControl($store, 1, 1);
            $this->todayControlsIds['sell']['evening'] = $this->controlsRepository->findTodayControl($store, 1, 2);
            $this->todayControlsIds['buy']['morning'] = $this->controlsRepository->findTodayControl($store, 2, 1);
            $this->todayControlsIds['buy']['evening'] = $this->controlsRepository->findTodayControl($store, 2, 2);
        }
    }

    /**
     * @return iterable
     */
    public function configureMenuItems(): iterable
    {
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
        // Deposits sales menu
        yield $this->configureDepositsSalesMenuItem();
        // Advances payments menu
        yield $this->configureAdvancesPaymentsMenuItem();
        // Invoices menu
        yield $this->configureInvoicesMenuItem();
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
                $this->getControlMenuItem(1, 1, 'sell', 'morning'),
                $this->getControlMenuItem(1, 2, 'sell', 'evening'),
                MenuItem::section()
                    ->setPermission(ControlsCrudController::ROLE_NEW_BUY),
                $this->getControlMenuItem(2, 1, 'buy', 'morning'),
                $this->getControlMenuItem(2, 2, 'buy', 'evening'),
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
     * @param int $counter
     * @param int $period
     * @param string $counterLabel
     * @param string $periodLabel
     * @return CrudMenuItem
     */
    private function getControlMenuItem(int $counter, int $period, string $counterLabel, string $periodLabel): CrudMenuItem
    {
        return !isset($this->todayControlsIds[$counterLabel][$periodLabel]) || !$this->todayControlsIds[$counterLabel][$periodLabel]
            ? MenuItem::linkToCrud('Controls.Create ' . $counterLabel . ' ' . $periodLabel . ' control', null, Controls::class)
                ->setQueryParameter('counter', $counter)
                ->setQueryParameter('period', $period)
                ->setAction(Crud::PAGE_NEW)
            : MenuItem::linkToCrud('Controls.Edit ' . $counterLabel . ' ' . $periodLabel . ' control', null, Controls::class)
                ->setQueryParameter('entityId', $this->todayControlsIds[$counterLabel][$periodLabel])
                ->setAction(Crud::PAGE_EDIT);
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
     * @return CrudMenuItem|SubMenuItem
     */
    private function configureDepositsSalesMenuItem(): CrudMenuItem|SubMenuItem
    {
        if ($this->isGranted(DepositsSalesCrudController::ROLE_NEW)) {
            return MenuItem::subMenu('Menu.DepositsSales', 'uil-moneybag-alt')->setSubItems([
                MenuItem::linkToCrud('DepositsSales.List of deposits sales', null, DepositsSales::class)
                    ->setAction(Crud::PAGE_INDEX),
                MenuItem::section(),
                MenuItem::linkToCrud('DepositsSales.Create deposit sales', null, DepositsSales::class)
                    ->setAction(Crud::PAGE_NEW)
            ]);
        }

        return MenuItem::linkToCrud('Menu.DepositsSales', 'uil-moneybag-alt', DepositsSales::class)
            ->setAction(Crud::PAGE_INDEX);
    }

    /**
     * @return CrudMenuItem|SubMenuItem
     */
    private function configureAdvancesPaymentsMenuItem(): CrudMenuItem|SubMenuItem
    {
        if ($this->isGranted(AdvancesPaymentsCrudController::ROLE_NEW)) {
            return MenuItem::subMenu('Menu.AdvancesPayments', 'uil-bill')->setSubItems([
                MenuItem::linkToCrud('AdvancesPayments.List of advances payments', null, AdvancesPayments::class)
                    ->setAction(Crud::PAGE_INDEX),
                MenuItem::section(),
                MenuItem::linkToCrud('AdvancesPayments.Create advance payments', null, AdvancesPayments::class)
                    ->setAction(Crud::PAGE_NEW),
                MenuItem::section()
                    ->setPermission(AdvancesPaymentsMethodsCrudController::ROLE_INDEX),
                MenuItem::linkToCrud('AdvancesPaymentsMethods.List of advances payments methods', null, AdvancesPaymentsMethods::class)
                    ->setAction(Crud::PAGE_INDEX)
                    ->setPermission(AdvancesPaymentsMethodsCrudController::ROLE_INDEX)
            ]);
        }

        return MenuItem::linkToCrud('Menu.AdvancesPayments', 'uil-bill', AdvancesPayments::class)
            ->setAction(Crud::PAGE_INDEX);
    }

    /**
     * @return CrudMenuItem|SubMenuItem
     */
    private function configureInvoicesMenuItem(): CrudMenuItem|SubMenuItem
    {
        if ($this->isGranted(AdvancesPaymentsCrudController::ROLE_NEW)) {
            return MenuItem::subMenu('Menu.Invoices', 'uil-receipt')->setSubItems([
                MenuItem::linkToCrud('Invoices.List of invoices', null, Invoices::class)
                    ->setAction(Crud::PAGE_INDEX),
                MenuItem::section(),
                MenuItem::linkToCrud('Invoices.Create invoice', null, Invoices::class)
                    ->setAction(Crud::PAGE_NEW),
                MenuItem::section()
                    ->setPermission(InvoicesTaxesRatesCrudController::ROLE_INDEX),
                MenuItem::linkToCrud('InvoicesTaxesRates.List of taxes rates', null, InvoicesTaxesRates::class)
                    ->setAction(Crud::PAGE_INDEX)
                    ->setPermission(InvoicesTaxesRatesCrudController::ROLE_INDEX)
            ]);
        }

        return MenuItem::linkToCrud('Menu.Invoices', 'uil-receipt', Invoices::class)
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
                MenuItem::section('Top menu.Welcome !'),
                MenuItem::linkToCrud('Top menu.My account', 'mdi mdi-account-circle', Users::class)
                    ->setAction(Crud::PAGE_DETAIL)
                    ->setEntityId($user->getId()),
                MenuItem::linkToUrl('Top menu.Support', 'mdi mdi-lifebuoy', 'tel:0618824312'),
                MenuItem::section(),
                MenuItem::linkToLogout('Top menu.Logout', 'mdi mdi-logout')
            ]);
    }

    public function configureActions(): Actions
    {
        return parent::configureActions()
            // Index page
            ->update(Crud::PAGE_INDEX, Action::NEW,
                fn (Action $action) => $action->setCssClass('action-new btn btn-success'))
            ->add(Crud::PAGE_INDEX, Action::DETAIL)
            ->update(Crud::PAGE_INDEX, Action::DETAIL,
                fn (Action $action) => $action->addCssClass('btn btn-outline-info btn-sm py-0 px-1 me-1'))
            ->update(Crud::PAGE_INDEX, Action::EDIT,
                fn (Action $action) => $action->addCssClass('btn btn-outline-warning btn-sm py-0 px-1 me-1'))
            ->update(Crud::PAGE_INDEX, Action::DELETE,
                fn (Action $action) => $action->setCssClass('action-delete btn btn-outline-danger btn-sm py-0 px-1'))

            // New page
            ->add(Crud::PAGE_NEW, Action::INDEX)
            ->update(Crud::PAGE_NEW, Action::INDEX,
                fn (Action $action) => $action->setCssClass('action-index btn btn-secondary'))
            ->update(Crud::PAGE_NEW, Action::SAVE_AND_ADD_ANOTHER,
                fn (Action $action) => $action->setCssClass('action-saveAndAddAnother btn btn-info action-save'))
            ->update(Crud::PAGE_NEW, Action::SAVE_AND_RETURN,
                fn (Action $action) => $action->setCssClass('action-saveAndReturn btn btn-success action-save'))

            // Edit page
            ->add(Crud::PAGE_EDIT, Action::INDEX)
            ->update(Crud::PAGE_EDIT, Action::INDEX,
                fn (Action $action) => $action->setCssClass('action-index btn btn-secondary'))
            ->update(Crud::PAGE_EDIT, Action::SAVE_AND_CONTINUE,
                fn (Action $action) => $action->setCssClass('action-saveAndContinue btn btn-info action-save'))
            ->update(Crud::PAGE_EDIT, Action::SAVE_AND_RETURN,
                fn (Action $action) => $action->setCssClass('action-saveAndReturn btn btn-warning action-save'))

            // Detail page
            ->update(Crud::PAGE_DETAIL, Action::DELETE,
                fn (Action $action) => $action->setCssClass('action-delete btn btn-danger text-white'))
            ->update(Crud::PAGE_DETAIL, Action::INDEX,
                fn (Action $action) => $action->setCssClass('action-index btn btn-secondary'))
            ->update(Crud::PAGE_DETAIL, Action::EDIT,
                fn (Action $action) => $action->setCssClass('action-edit btn btn-warning')
                    ->displayIf(fn ($entity) => $this->isGranted('ROLE_ADMIN')
                        || $this->getUser()->getId() === $entity->getId()));
    }
}
