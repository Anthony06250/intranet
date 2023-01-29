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
        'one_cent' => 0.01,
        'two_cents' => 0.02,
        'five_cents' => 0.05,
        'ten_cents' => 0.1,
        'twenty_cents' => 0.2,
        'fifty_cents' => 0.5,
        'one_euro' => 1,
        'two_euros' => 2,
        'five_euros' => 5,
        'ten_euros' => 10,
        'twenty_euros' => 20,
        'fifty_euros' => 50,
        'one_hundred_euros' => 100,
        'two_hundred_euros' => 200,
        'five_hundred_euros' => 500
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
                'created_at' => 'DESC'
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
        $assets
            ->addJsFile(Asset::new('assets/js/page/page.controls.js')
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
        yield $this->getControlsCountersField();
        yield $this->getControlsPeriodsField();

        yield DateTimeField::new('created_at', 'Forms.Labels.Created at')
            ->setFormTypeOption('attr', [
                'readonly' => !$this->isGranted('ROLE_ADMIN')
            ]);
        yield DateTimeField::new('updated_at', 'Forms.Labels.Updated at')
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
        yield MoneyField::new('cash_fund', 'Forms.Labels.Cash fund')
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
    private function getControlsCountersField(): AssociationField
    {
        $countersField = AssociationField::new('controlsCounter', 'Forms.Labels.Counter')
            ->setFormTypeOptions([
                'choice_attr' => function ($choice) {
                    return [
                        'data-reverse' => $choice->isReverse(),
                        'data-cash-fund' => $choice->getCashFund()
                    ];
                }
            ]);

        if (!$this->isGranted(self::ROLE_NEW_BUY) || isset($_GET['controlsCounter'])) {
            $countersField->setFormTypeOptions([
                'query_builder' => function (ControlsCountersRepository $countersRepository) {
                    return $countersRepository->createQueryBuilder('c')
                        ->where('c.id = ' . ($_GET['controlsCounter'] ?? '1'));
                },
            ]);
        }

        return $countersField;
    }

    /**
     * @return AssociationField
     */
    private function getControlsPeriodsField(): AssociationField
    {
        $periodsField = AssociationField::new('controlsPeriod', 'Forms.Labels.Period');

        if (isset($_GET['controlsPeriod'])) {
            $periodsField->setFormTypeOptions([
                'query_builder' => function (ControlsPeriodsRepository $periodsRepository) {
                    return $periodsRepository->createQueryBuilder('p')
                        ->where('p.id = ' . $_GET['controlsPeriod']);
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
        return IntegerField::new($name, 'Forms.Labels.' . ucfirst(str_replace('_', ' ', $name)))
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
            ->update(Crud::PAGE_INDEX, Action::NEW,
                fn (Action $action) => $action->setCssClass('action-new btn btn-success'))
            ->add(Crud::PAGE_INDEX, Action::DETAIL)
            ->update(Crud::PAGE_INDEX, Action::DETAIL,
                fn (Action $action) => $action->addCssClass('btn btn-outline-info btn-sm py-0 px-1 me-1'))
            ->update(Crud::PAGE_INDEX, Action::EDIT,
                fn (Action $action) => $action->addCssClass('btn btn-outline-warning btn-sm py-0 px-1 me-1')
                    ->displayIf(fn ($entity) => $this->isGranted('ROLE_ADMIN')
                        || $this->getUser()->getId() === $entity->getUser()->getId()))
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
