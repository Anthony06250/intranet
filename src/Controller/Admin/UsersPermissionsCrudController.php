<?php

namespace App\Controller\Admin;

use App\Entity\UsersPermissions;
use App\Form\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;

class UsersPermissionsCrudController extends AbstractCrudController
{
    /**
     * Crud permissions
     */
    public const ROLE_INDEX = 'ROLE_ADMIN';
    public const ROLE_NEW = 'ROLE_SUPER_ADMIN';
    public const ROLE_EDIT = 'ROLE_SUPER_ADMIN';
    public const ROLE_DELETE = 'ROLE_SUPER_ADMIN';

    /**
     * @return string
     */
    public static function getEntityFqcn(): string
    {
        return UsersPermissions::class;
    }

    /**
     * @param Crud $crud
     * @return Crud
     */
    public function configureCrud(Crud $crud): Crud
    {
        return $crud
            ->setEntityLabelInSingular('UsersPermissions.UsersPermission')
            ->setEntityLabelInPlural('UsersPermissions.UsersPermissions')
            ->setPageTitle(Crud::PAGE_INDEX, 'UsersPermissions.List of users permissions')
            ->setPageTitle(Crud::PAGE_NEW, 'UsersPermissions.Create users permission')
            ->setPageTitle(Crud::PAGE_EDIT, 'UsersPermissions.Edit users permission')
            ->setPageTitle(Crud::PAGE_DETAIL, 'UsersPermissions.View users permission')
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
        yield TextField::new('role', 'Forms.Labels.Role');
    }

    /**
     * @param Actions $actions
     * @return Actions
     */
    public function configureActions(Actions $actions): Actions
    {
        // Permissions
        return $actions->setPermissions([
            Action::INDEX => self::ROLE_INDEX,
            Action::NEW => self::ROLE_NEW,
            Action::EDIT => self::ROLE_EDIT,
            Action::DELETE => self::ROLE_DELETE
        ]);
    }
}
