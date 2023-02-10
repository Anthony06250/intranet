<?php

namespace App\Controller\Admin;

use App\Entity\Controls;
use App\Form\Field\AssociationField;
use App\Form\Field\DateTimeField;
use App\Form\Field\IntegerField;
use App\Form\Field\MoneyField;
use App\Repository\ControlsCountersRepository;
use App\Repository\ControlsPeriodsRepository;
use App\Repository\StoresRepository;
use App\Repository\UsersRepository;
use Doctrine\ORM\QueryBuilder;
use EasyCorp\Bundle\EasyAdminBundle\Collection\FieldCollection;
use EasyCorp\Bundle\EasyAdminBundle\Collection\FilterCollection;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Config\Asset;
use EasyCorp\Bundle\EasyAdminBundle\Config\Assets;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Config\KeyValueStore;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Dto\EntityDto;
use EasyCorp\Bundle\EasyAdminBundle\Dto\SearchDto;

class ControlsCrudController extends AbstractCrudController
{
    /**
     * Index page settings
     */
    public const MAX_RESULTS_REQUEST = 320;
    public const RESULTS_PER_PAGE = 20;

    /**
     * Counts fields data
     */
    public const COUNTS_FIELDS_DATA = [
        'oneCent' => 0.01,
        'twoCents' => 0.02,
        'fiveCents' => 0.05,
        'tenCents' => 0.1,
        'twentyCents' => 0.2,
        'fiftyCents' => 0.5,
        'oneEuro' => 1,
        'twoEuros' => 2,
        'fiveEuros' => 5,
        'tenEuros' => 10,
        'twentyEuros' => 20,
        'fiftyEuros' => 50,
        'oneHundredEuros' => 100,
        'twoHundredEuros' => 200,
        'fiveHundredEuros' => 500
    ];

    /**
     * Crud permissions
     */
    public const ROLE_INDEX = 'ROLE_SELLER';
    public const ROLE_NEW_SELL = 'ROLE_SELLER';
    public const ROLE_NEW_BUY = 'ROLE_BUYER';
    public const ROLE_EDIT = 'ROLE_JEWELER';
    public const ROLE_DELETE = 'ROLE_ADMIN';

    /**
     * @return string
     */
    public static function getEntityFqcn(): string
    {
        return Controls::class;
    }

    /**
     * @param SearchDto $searchDto
     * @param EntityDto $entityDto
     * @param FieldCollection $fields
     * @param FilterCollection $filters
     * @return QueryBuilder
     */
    public function createIndexQueryBuilder(SearchDto $searchDto, EntityDto $entityDto, FieldCollection $fields, FilterCollection $filters): QueryBuilder
    {
        $queryBuilder = parent::createIndexQueryBuilder($searchDto, $entityDto, $fields, $filters);

        if (!$this->isGranted('ROLE_ADMIN')) {
            $queryBuilder->where('entity.store IN (:ids)')
                ->setParameter('ids', $this->getStoresForUser());
        }

        return $queryBuilder;
    }

    /**
     * @param Crud $crud
     * @return Crud
     */
    public function configureCrud(Crud $crud): Crud
    {
        return $crud
            ->setEntityLabelInSingular('Controls.Control')
            ->setEntityLabelInPlural('Controls.Controls')
            ->setPageTitle(Crud::PAGE_INDEX, 'Controls.List of controls')
            ->setPageTitle(Crud::PAGE_NEW, 'Controls.Create control')
            ->setPageTitle(Crud::PAGE_EDIT, 'Controls.Edit control')
            ->setPageTitle(Crud::PAGE_DETAIL, 'Controls.View control')
            ->setDefaultSort([
                'createdAt' => 'DESC'
            ])
            ->setPaginatorPageSize(self::MAX_RESULTS_REQUEST)
            ->showEntityActionsInlined()
            ->overrideTemplates([
                'crud/new' => 'bundles/EasyAdminBundle/crud/controls.html.twig',
                'crud/edit' => 'bundles/EasyAdminBundle/crud/controls.html.twig'
            ]);
    }

    /**
     * @param Assets $assets
     * @return Assets
     */
    public function configureAssets(Assets $assets): Assets
    {
        $assets->addJsFile(Asset::new('assets/js/page/page.controls.js')
            ->onlyOnForms());

        return $assets;
    }

    /**
     * @param KeyValueStore $responseParameters
     * @return KeyValueStore
     */
    public function configureResponseParameters(KeyValueStore $responseParameters): KeyValueStore
    {
        if (Crud::PAGE_INDEX === $responseParameters->get('pageName')) {
            $responseParameters->set('pageLimit', self::RESULTS_PER_PAGE);
        }

        return $responseParameters;
    }

    /**
     * @param string $pageName
     * @return iterable
     */
    public function configureFields(string $pageName): iterable
    {
        yield $this->getUsersField();
        yield $this->getStoresField();
        yield $this->getCountersField();
        yield $this->getPeriodsField();

        yield DateTimeField::new('createdAt', 'Forms.Labels.Created at')
            ->setFormTypeOption('attr', [
                'readonly' => !$this->isGranted('ROLE_ADMIN')
            ]);
        yield DateTimeField::new('updatedAt', 'Forms.Labels.Updated at')
            ->hideOnIndex()
            ->setFormTypeOption('attr', [
                'readonly' => true
            ]);
        yield MoneyField::new('turnover', 'Forms.Labels.Turnover')
            ->hideOnIndex()
            ->setFormTypeOptions([
                'required' => false,
                'empty_data' => '0'
            ]);
        yield MoneyField::new('cashFund', 'Forms.Labels.Cash fund')
            ->hideOnIndex()
            ->setFormTypeOptions([
                'required' => false,
                'empty_data' => '0'
            ]);

        foreach (self::COUNTS_FIELDS_DATA as $name => $calc) {
            yield $this->getCountsFields($name, $calc);
        }

        yield MoneyField::new('result', 'Forms.Labels.Result')
            ->hideOnIndex()
            ->setFormTypeOptions([
                'attr' => [
                    'readonly' => true
                ],
                'empty_data' => '0'
            ]);
        yield MoneyField::new('error', 'Forms.Labels.Error')
            ->setFormTypeOptions([
                'attr' => [
                    'readonly' => true
                ],
                'empty_data' => '0'
            ]);
    }

    /**
     * @return AssociationField
     */
    private function getUsersField(): AssociationField
    {
        $usersField = AssociationField::new('user', 'Forms.Labels.User')
            ->addCssClass('fw-bold');

        if (!$this->isGranted('ROLE_ADMIN')) {
            $usersField->setFormTypeOptions([
                'query_builder' => function (UsersRepository $usersRepository) {
                    return $usersRepository->createQueryBuilder('u')
                        ->where('u.id = (:id)')
                        ->setParameter('id', $this->getUser()->getId());
                }
            ]);
        }

        return $usersField;
    }

    /**
     * @return AssociationField
     */
    private function getStoresField(): AssociationField
    {
        $storesField = AssociationField::new('store', 'Forms.Labels.Store');

        if (!$this->isGranted('ROLE_ADMIN')) {
            $storesField->setFormTypeOptions([
                'query_builder' => function (StoresRepository $storesRepository) {
                    return $storesRepository->createQueryBuilder('s')
                        ->where('s.id IN (:ids)')
                        ->setParameter('ids', $this->getStoresForUser());
                }
            ]);
        }

        return $storesField;
    }

    /**
     * @return array
     */
    private function getStoresForUser(): array
    {
        $stores = [];

        foreach ($this->getUser()->getStores() as $store) {
            $stores[] = $store->getId();
        }

        return $stores;
    }

    /**
     * @return AssociationField
     */
    private function getCountersField(): AssociationField
    {
        $countersField = AssociationField::new('counter', 'Forms.Labels.Counter')
            ->setFormTypeOptions([
                'choice_attr' => function ($choice) {
                    return [
                        'data-reverse' => $choice->isReverse(),
                        'data-cash-fund' => $choice->getCashFund()
                    ];
                }
            ]);

        if (!$this->isGranted(self::ROLE_NEW_BUY) || isset($_GET['counter'])) {
            $countersField->setFormTypeOptions([
                'query_builder' => function (ControlsCountersRepository $countersRepository) {
                    return $countersRepository->createQueryBuilder('c')
                        ->where('c.id = ' . ($_GET['counter'] ?? '1'));
                },
            ]);
        }

        return $countersField;
    }

    /**
     * @return AssociationField
     */
    private function getPeriodsField(): AssociationField
    {
        $periodsField = AssociationField::new('period', 'Forms.Labels.Period')
            ->setFormTypeOptions([
                'choice_attr' => function ($choice) {
                    return [
                        'data-debit' => $choice->isDebit()
                    ];
                }
            ]);

        if (isset($_GET['period'])) {
            $periodsField->setFormTypeOptions([
                'query_builder' => function (ControlsPeriodsRepository $periodsRepository) {
                    return $periodsRepository->createQueryBuilder('p')
                        ->where('p.id = ' . $_GET['period']);
                }
            ]);
        }

        return $periodsField;
    }

    /**
     * @param string $name
     * @param float $calc
     * @return IntegerField
     */
    private function getCountsFields(string $name, float $calc): IntegerField
    {
        $label = ucfirst(strtolower(preg_replace(array('/(?<=[^A-Z])([A-Z])/', '/(?<=[^0-9])([0-9])/'), ' $0', $name)));

        return IntegerField::new($name, 'Forms.Labels.' . $label)
            ->hideOnIndex()
            ->setFormTypeOption('attr', [
                'min' => 0,
                'data-calc' => $calc
            ])
            ->setColumns('col-4');
    }

    /**
     * @param Actions $actions
     * @return Actions
     */
    public function configureActions(Actions $actions): Actions
    {
        return $actions
            // Index page
            ->update(Crud::PAGE_INDEX, Action::EDIT,
                fn (Action $action) => $action->displayIf(fn ($entity) => $this->isGranted(self::ROLE_DELETE)
                    || $this->getUser()->getId() === $entity->getUser()->getId()))

            // Permissions
            ->setPermissions([
                Action::INDEX => self::ROLE_INDEX,
                Action::NEW => self::ROLE_NEW_SELL,
                Action::EDIT => self::ROLE_EDIT,
                Action::DELETE => self::ROLE_DELETE
            ]);
    }
}
