<?php

namespace App\Controller\Admin;

use App\Entity\SafesMovementsTypes;
use App\Form\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;

class SafesMovementsTypesCrudController extends AbstractCrudController
{
    /**
     * Crud permissions
     */
    public const ROLE_INDEX = 'ROLE_ADMIN';
    public const ROLE_NEW = 'ROLE_ADMIN';
    public const ROLE_EDIT = 'ROLE_ADMIN';
    public const ROLE_DELETE = 'ROLE_SUPER_ADMIN';

    /**
     * @return string
     */
    public static function getEntityFqcn(): string
    {
        return SafesMovementsTypes::class;
    }

    /**
     * @param Crud $crud
     * @return Crud
     */
    public function configureCrud(Crud $crud): Crud
    {
        return $crud
            ->setEntityLabelInSingular('SafesMovementsTypes.SafesMovementsType')
            ->setEntityLabelInPlural('SafesMovementsTypes.SafesMovementsTypes')
            ->setPageTitle(Crud::PAGE_INDEX, 'SafesMovementsTypes.List of safes movements types')
            ->setPageTitle(Crud::PAGE_NEW, 'SafesMovementsTypes.Create safes movements type')
            ->setPageTitle(Crud::PAGE_EDIT, 'SafesMovementsTypes.Edit safes movements type')
            ->setPageTitle(Crud::PAGE_DETAIL, 'SafesMovementsTypes.View safes movements type')
            ->showEntityActionsInlined();
    }

    /**
     * @param string $pageName
     * @return iterable
     */
    public function configureFields(string $pageName): iterable
    {
        yield TextField::new('label', 'Forms.Labels.Label')
            ->addCssClass('fw-bold');
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
}
