<?php

namespace App\Controller\Admin;

use App\Entity\SafesMovements;
use App\Form\Field\AssociationField;
use App\Form\Field\DateTimeField;
use App\Form\Field\MoneyField;
use App\Form\Field\TextareaField;
use Doctrine\ORM\QueryBuilder;
use EasyCorp\Bundle\EasyAdminBundle\Collection\FieldCollection;
use EasyCorp\Bundle\EasyAdminBundle\Collection\FilterCollection;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Config\KeyValueStore;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Dto\EntityDto;
use EasyCorp\Bundle\EasyAdminBundle\Dto\SearchDto;

class SafesMovementsCrudController extends AbstractCrudController
{
    /**
     * Index page settings
     */
    public const MAX_RESULTS_REQUEST = 320;
    public const RESULTS_PER_PAGE = 20;

    /**
     * Crud permissions
     */
    public const ROLE_INDEX = 'ROLE_MANAGER';
    public const ROLE_NEW = 'ROLE_MANAGER';
    public const ROLE_EDIT = 'ROLE_MANAGER';
    public const ROLE_DELETE = 'ROLE_ADMIN';

    /**
     * @return string
     */
    public static function getEntityFqcn(): string
    {
        return SafesMovements::class;
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
            ->setEntityLabelInSingular('SafesMovements.SafesMovement')
            ->setEntityLabelInPlural('SafesMovements.SafesMovements')
            ->setPageTitle(Crud::PAGE_INDEX, 'SafesMovements.List of safes movements')
            ->setPageTitle(Crud::PAGE_NEW, 'SafesMovements.Create safes movement')
            ->setPageTitle(Crud::PAGE_EDIT, 'SafesMovements.Edit safes movement')
            ->setPageTitle(Crud::PAGE_DETAIL, 'SafesMovements.View safes movement')
            ->setDefaultSort([
                'created_at' => 'DESC'
            ])
            ->setPaginatorPageSize(self::MAX_RESULTS_REQUEST)
            ->showEntityActionsInlined();
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
        yield AssociationField::new('user', 'Forms.Labels.User')
            ->addCssClass('fw-bold');
        yield AssociationField::new('store', 'Forms.Labels.Store');
        yield MoneyField::new('amount', 'Forms.Labels.Amount');
        yield AssociationField::new('safesMovementsType', 'Forms.Labels.Movements type');
        yield DateTimeField::new('created_at', 'Forms.Labels.Created at');
        yield TextareaField::new('comments', 'Forms.Labels.Comments')
            ->setColumns('col-12');
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
                Action::NEW => self::ROLE_NEW,
                Action::EDIT => self::ROLE_EDIT,
                Action::DELETE => self::ROLE_DELETE
            ]);
    }
}
