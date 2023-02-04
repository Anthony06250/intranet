<?php

namespace App\Controller\Admin;

use App\Entity\AdvancesPaymentsMethods;
use App\Form\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;

class AdvancesPaymentsMethodsCrudController extends AbstractCrudController
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
        return AdvancesPaymentsMethods::class;
    }

    /**
     * @param Crud $crud
     * @return Crud
     */
    public function configureCrud(Crud $crud): Crud
    {
        return $crud
            ->setEntityLabelInSingular('AdvancesPaymentsMethods.AdvancesPaymentMethods')
            ->setEntityLabelInPlural('AdvancesPaymentsMethods.AdvancesPaymentsMethods')
            ->setPageTitle(Crud::PAGE_INDEX, 'AdvancesPaymentsMethods.List of advances payments methods')
            ->setPageTitle(Crud::PAGE_NEW, 'AdvancesPaymentsMethods.Create advances payment methods')
            ->setPageTitle(Crud::PAGE_EDIT, 'AdvancesPaymentsMethods.Edit advances payment methods')
            ->setPageTitle(Crud::PAGE_DETAIL, 'AdvancesPaymentsMethods.View advances payment methods')
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
