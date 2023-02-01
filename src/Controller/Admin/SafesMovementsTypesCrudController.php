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
        // Permissions
        return $actions->setPermissions([
            Action::INDEX => self::ROLE_INDEX,
            Action::NEW => self::ROLE_NEW,
            Action::EDIT => self::ROLE_EDIT,
            Action::DELETE => self::ROLE_DELETE
        ]);
    }
}
