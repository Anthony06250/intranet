<?php

namespace App\Controller\Admin;

use App\Entity\ControlsCounters;
use App\Entity\SafesControls;
use App\Form\Field\AssociationField;
use App\Form\Field\DateTimeField;
use App\Form\Field\IntegerField;
use App\Form\Field\MoneyField;
use App\Repository\ControlsCountersRepository;
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
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Dto\EntityDto;
use EasyCorp\Bundle\EasyAdminBundle\Dto\SearchDto;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;

class SafesControlsCrudController extends AbstractCrudController
{
    /**
     * Crud permissions
     */
    public const ROLE_INDEX = 'ROLE_SELLER';
    public const ROLE_NEW = 'ROLE_MANAGER';
    public const ROLE_EDIT = 'ROLE_MANAGER';
    public const ROLE_DELETE = 'ROLE_ADMIN';

    /**
     * @return string
     */
    public static function getEntityFqcn(): string
    {
        return SafesControls::class;
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
            ->setEntityLabelInSingular('SafesControls.SafesControl')
            ->setEntityLabelInPlural('SafesControls.SafesControls')
            ->setPageTitle(Crud::PAGE_INDEX, 'SafesControls.List of safes controls')
            ->setPageTitle(Crud::PAGE_NEW, 'SafesControls.Create safes control')
            ->setPageTitle(Crud::PAGE_EDIT, 'SafesControls.Edit safes control')
            ->setPageTitle(Crud::PAGE_DETAIL, 'SafesControls.View safes control')
            ->showEntityActionsInlined()
            ->overrideTemplates([
                'crud/new' => 'bundles/EasyAdminBundle/crud/safes-controls.html.twig',
                'crud/edit' => 'bundles/EasyAdminBundle/crud/safes-controls.html.twig'
            ]);
    }

    /**
     * @param Assets $assets
     * @return Assets
     */
    public function configureAssets(Assets $assets): Assets
    {
        $assets
            ->addJsFile(Asset::new('assets/js/page/page.safes-controls.js')
                ->onlyOnForms());

        return $assets;
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
     * @param string $pageName
     * @return iterable
     */
    public function configureFields(string $pageName): iterable
    {
        yield $this->getUsersField();
        yield $this->getStoresField();
        yield $this->getControlsCountersField();

        yield DateTimeField::new('created_at', 'Forms.Labels.Created at')
            ->setFormTypeOption('attr', [
                'readonly' => !$this->isGranted('ROLE_ADMIN')
            ]);

        foreach (ControlsCrudController::COUNTS_FIELDS_DATA as $name => $calc) {
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
        yield MoneyField::new('total', 'Forms.Labels.Total')
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
     * @return AssociationField
     */
    private function getControlsCountersField(): AssociationField
    {
        $countersField = AssociationField::new('controlsCounters', 'Forms.Labels.Counter')
            ->setFormTypeOptions([
                'choice_attr' => function ($choice) {
                    return [
                        'data-cash-fund' => $choice->getCashFund()
                    ];
                }
            ]);

        if (!$this->isGranted(self::ROLE_NEW) || isset($_GET['controlsCounter'])) {
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
                fn (Action $action) => $action->setCssClass('action-edit btn btn-warning'))

            // Permissions
            ->setPermissions([
                Action::INDEX => self::ROLE_INDEX,
                Action::NEW => self::ROLE_NEW,
                Action::EDIT => self::ROLE_EDIT,
                Action::DELETE => self::ROLE_DELETE
            ]);
    }

    /**
     * @param string $entityFqcn
     * @return mixed
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    public function createEntity(string $entityFqcn): mixed
    {
        $safesControl = parent::createEntity($entityFqcn);
        $controlsCounterRepository = $this->container->get('doctrine')->getRepository(ControlsCounters::class);

        for ($i = 1; $i <= 2; $i++) {
            $controlsCounter = $controlsCounterRepository->find($i);

            $safesControl->addControlsCounters($controlsCounter);
        }

        return $safesControl;
    }
}
